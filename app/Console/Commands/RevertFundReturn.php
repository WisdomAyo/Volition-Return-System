<?php

namespace App\Console\Commands;

use App\Models\FundReturn;
use App\Services\FundReturnService;
use Illuminate\Console\Command;

class RevertFundReturn extends Command
{
    protected $signature = 'fund:revert-return {return_id : ID of the return to revert}';
    protected $description = 'Revert a fund return';

    public function handle(FundReturnService $service): int
    {
        $return = FundReturn::findOrFail($this->argument('return_id'));

        try {
            $service->revertReturn($return);
            $this->info("Return reverted successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}