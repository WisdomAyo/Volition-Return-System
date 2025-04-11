<?php

namespace App\Console\Commands;

use App\Models\Fund;
use App\Services\FundReturnService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetFundValue extends Command
{  
    protected $signature = 'fund:value
        {fund_id : ID of the fund}
        {date : Date to get value for (YYYY-MM-DD)}';

    protected $description = 'Get fund value at specific date';

    public function handle(FundReturnService $service): int
    {
        $fund = Fund::findOrFail($this->argument('fund_id'));
        $date = Carbon::parse($this->argument('date'));

        try {
            $value = $service->getFundValueAtDateOptimized($fund, $date);
            $this->info("Fund value on {$date->toDateString()}: {$value} {$fund->currency}");
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
