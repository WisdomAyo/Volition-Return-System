<?php

namespace App\Events;

use App\Models\FundReturn;
use Illuminate\Foundation\Events\Dispatchable;

class FundReturnAdded
{
    use Dispatchable;

    public function __construct(public FundReturn $fundReturn)
    {
    }
}