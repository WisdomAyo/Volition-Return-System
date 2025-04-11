<?php

namespace Tests\Unit;

use App\Models\Fund;
use App\Services\FundReturnService;
use Carbon\Carbon;
use Tests\TestCase;

class FundReturnServiceTest extends TestCase
{
    private FundReturnService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FundReturnService::class);
    }

    public function test_non_compounding_calculation()
    {
        $result = $this->service->calculateNewValue(
            1000, // current value
            10,   // 10% return
            false, // non-compounding
            1000   // original value
        );

        $this->assertEquals(1100, $result);
    }

    public function test_compounding_calculation()
    {
        $result = $this->service->calculateNewValue(
            1100, // current value
            10,   // 10% return
            true,  // compounding
            1000   // original value (not used)
        );

        $this->assertEquals(1210, $result);
    }

    public function test_get_value_at_date_with_no_returns()
    {
        $fund = Fund::factory()->create(['starting_balance' => 1000]);
        $date = Carbon::parse('2023-01-01');

        $value = $this->service->getFundValueAtDate($fund, $date);
        $this->assertEquals(1000, $value);
    }
}