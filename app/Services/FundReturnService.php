<?php

namespace App\Services;

use App\Events\FundReturnAdded;
use App\Events\FundReturnReverted;
use App\Exceptions\FundOperationException;
use App\Models\Fund;
use App\Models\FundReturn;
use App\Models\FundValueSnapshot;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class FundReturnService
{
    public function addReturn(Fund $fund, array $data): FundReturn
    {
        $this->validateReturnDate($fund, Carbon::parse($data['date']));

        return DB::transaction(function () use ($fund, $data) {
            $date = Carbon::parse($data['date']);
            $returnPercentage = $data['return_percentage'];
            $isCompounding = $data['is_compounding'] ?? false;

            $previousReturn = $fund->returns()
                ->where('date', '<', $date)
                ->where('reverted', false)
                ->latest('date')
                ->first();

            $valueBefore = $previousReturn
                ? $previousReturn->value_after
                : $fund->starting_balance;

            $valueAfter = $this->calculateNewValue(
                $valueBefore,
                $returnPercentage,
                $isCompounding,
                $fund->starting_balance
            );

            $return = $fund->returns()->create([
                'date' => $date,
                'frequency' => $data['frequency'],
                'return_percentage' => $returnPercentage,
                'is_compounding' => $isCompounding,
                'value_before' => $valueBefore,
                'value_after' => $valueAfter,
                'notes' => $data['notes'] ?? null,
            ]);

            event(new FundReturnAdded($return));

            return $return;
        });
    }

    public function revertReturn(FundReturn $fundReturn): void
    {
        try {
            DB::transaction(function () use ($fundReturn) {
                $fundReturn->update(['reverted' => true]);

            // Recalculate subsequent returns if any
            $subsequentReturns = $fundReturn->fund->returns()
                ->where('date', '>', $fundReturn->date)
                ->where('reverted', false)
                ->orderBy('date')
                ->get();

            $previousValue = $fundReturn->value_before;

            foreach ($subsequentReturns as $subsequentReturn) {
                $newValueAfter = $this->calculateNewValue(
                    $previousValue,
                    $subsequentReturn->return_percentage,
                    $subsequentReturn->is_compounding,
                    $subsequentReturn->fund->starting_balance
                );

                $subsequentReturn->update([
                    'value_before' => $previousValue,
                    'value_after' => $newValueAfter,
                ]);

                $previousValue = $newValueAfter;
            }

                event(new FundReturnReverted($fundReturn));
            });
        } catch (Exception $e) {
            Log::error("Error reverting fund return: " . $e->getMessage());
            throw new FundOperationException($e->getMessage());
        }
    }

    public function getFundValueAtDate(Fund $fund, Carbon $date): float
    {
        $lastReturnBeforeDate = $fund->returns()
            ->where('date', '<=', $date)
            ->where('reverted', false)
            ->latest('date')
            ->first();

        return $lastReturnBeforeDate
            ? $lastReturnBeforeDate->value_after
            : $fund->starting_balance;
    }

    public function getFundValueAtDateOptimized(Fund $fund, Carbon $date): float
    {
        $snapshot = $fund->snapshots()
            ->where('date', '<=', $date)
            ->orderByDesc('date')
            ->first();

        if ($snapshot) {
            $returns = $fund->returns()
                ->where('date', '>', $snapshot->date)
                ->where('date', '<=', $date)
                ->where('reverted', false)
                ->orderBy('date')
                ->get();

            $currentValue = $snapshot->value;

            foreach ($returns as $return) {
                $currentValue = $this->calculateNewValue(
                    $currentValue,
                    $return->return_percentage,
                    $return->is_compounding,
                    $fund->starting_balance
                );
            }

            return $currentValue;
        }

        return $this->getFundValueAtDate($fund, $date);
    }

    public function calculateProjectedValue(
        Fund $fund,
        Carbon $startDate,
        Carbon $endDate,
        float $returnPercentage,
        bool $isCompounding,
        string $frequency
    ): array {
        $currentDate = clone $startDate;
        $currentValue = $this->getFundValueAtDate($fund, $startDate);
        $originalValue = $currentValue;
        $projections = [];

        while ($currentDate <= $endDate) {
            $currentValue = $this->calculateNewValue(
                $currentValue,
                $returnPercentage,
                $isCompounding,
                $originalValue
            );

            $projections[] = [
                'date' => clone $currentDate,
                'value' => $currentValue,
                'return_percentage' => $returnPercentage,
            ];

            match ($frequency) {
                'monthly' => $currentDate->addMonth(),
                'quarterly' => $currentDate->addMonths(3),
                'yearly' => $currentDate->addYear(),
            };
        }

        return $projections;
    }

    public function validateReturnDate(Fund $fund, Carbon $date): void
    {
        $latestReturn = $fund->returns()
            ->where('reverted', false)
            ->latest('date')
            ->first();

        if ($latestReturn && $date->lessThan($latestReturn->date)) {
            throw new FundOperationException(
                "New return date ({$date->format('Y-m-d')}) cannot be earlier than the latest return date ({$latestReturn->date->format('Y-m-d')})"
            );
        }
    }

        public function calculateNewValue(
            float $currentValue,
            float $returnPercentage,
            bool $isCompounding,
            float $originalValue
        ): float {
            $baseValue = $isCompounding ? $currentValue : $originalValue;
            return $currentValue + ($baseValue * $returnPercentage / 100);
        }
}