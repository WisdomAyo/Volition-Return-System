<?php

namespace App\Listeners;

use App\Events\FundReturnAdded;
use App\Events\FundReturnReverted;

class LogFundReturnActivity
{
    public function handle(FundReturnAdded|FundReturnReverted $event): void
    {
        $action = $event instanceof FundReturnAdded ? 'added' : 'reverted';

        activity()
            ->performedOn($event->fundReturn)
            ->withProperties([
                'fund_id' => $event->fundReturn->fund_id,
                'value_before' => $event->fundReturn->value_before,
                'value_after' => $event->fundReturn->value_after,
                'date' => $event->fundReturn->date,
            ])
            ->log("Fund return {$action}");
    }
}