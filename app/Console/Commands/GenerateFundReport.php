<?php

namespace App\Console\Commands;

use App\Models\Fund;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateFundReport extends Command
{
    protected $signature = 'fund:report
        {fund_id : ID of the fund}
        {--from= : Start date (YYYY-MM-DD)} 
        {--to= : End date (YYYY-MM-DD)}';

    protected $description = 'Generate a fund performance report';

    public function handle(): int
    {
        $fund = Fund::findOrFail($this->argument('fund_id'));
        $from = $this->option('from') ? Carbon::parse($this->option('from')) : null;
        $to = $this->option('to') ? Carbon::parse($this->option('to')) : now();

        $query = $fund->returns()
            ->where('reverted', false)
            ->where('date', '<=', $to);

        if ($from) {
            $query->where('date', '>=', $from);
        }

        $returns = $query->orderBy('date')->get();

        $this->table(
            ['Date', 'Frequency', 'Return %', 'Type', 'Value Before', 'Value After'],
            $returns->map(function ($return) {
                return [
                    $return->date->format('Y-m-d'),
                    $return->frequency,
                    $return->return_percentage,
                    $return->is_compounding ? 'Compounding' : 'Simple',
                    number_format($return->value_before, 2),
                    number_format($return->value_after, 2),
                ];
            })
        );

        $currentValue = $fund->currentValue();
        $growth = $currentValue - $fund->starting_balance;
        $growthPercentage = ($growth / $fund->starting_balance) * 100;

        $this->newLine();
        $this->info("Summary:");
        $this->line("Starting Balance: " . number_format($fund->starting_balance, 2) . " {$fund->currency}");
        $this->line("Current Value: " . number_format($currentValue, 2) . " {$fund->currency}");
        $this->line("Total Growth: " . number_format($growth, 2) . " {$fund->currency} ({$growthPercentage}%)");

        return 0;
    }
}