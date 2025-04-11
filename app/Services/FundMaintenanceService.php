<?php

namespace App\Services;

use App\Models\Fund;
use App\Models\FundReturn;
use App\Services\FundReturnService;
use Carbon\Carbon;


class FundMaintenanceService
{
    public function generateMonthlySnapshots(): void
    {
        $date = Carbon::now()->endOfMonth();

        Fund::chunk(100, function ($funds) use ($date) {
            foreach ($funds as $fund) {
                $value = app(FundReturnService::class)
                    ->getFundValueAtDate($fund, $date);

                $fund->snapshots()->updateOrCreate(
                    ['date' => $date],
                    ['value' => $value]
                );
            }
        });
    }

    public function cleanupRevertedReturns(): void
    {
        FundReturn::where('reverted', true)
            ->where('created_at', '<', now()->subMonths(6))
            ->delete();
    }
}