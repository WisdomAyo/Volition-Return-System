<?php

namespace App\Console\Commands;

use App\Models\Fund;
use App\Services\FundReturnService;
use Illuminate\Console\Command;

class AddFundReturn extends Command
{
    protected $signature = 'fund:add-return
        {fund_id : ID of the fund}
        {date : Date of the return (YYYY-MM-DD)}
        {percentage : Return percentage}
        {--frequency=monthly : Return frequency (monthly, quarterly, yearly)}
        {--compounding : Whether the return is compounding}
        {--notes= : Optional notes}';

    protected $description = 'Add a return to a fund';

    public function handle(FundReturnService $service): int
    {
        $fund = Fund::findOrFail($this->argument('fund_id'));

        try {
            $return = $service->addReturn($fund, [
                'date' => $this->argument('date'),
                'frequency' => $this->option('frequency'),
                'return_percentage' => $this->argument('percentage'),
                'is_compounding' => $this->option('compounding'),
                'notes' => $this->option('notes'),  
            ]);

            $this->info("Return added successfully. New fund value: {$return->value_after}");
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
