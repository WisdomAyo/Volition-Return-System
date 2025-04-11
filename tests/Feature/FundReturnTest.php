<?php

namespace Tests\Feature;

use App\Models\Fund;
use App\Models\FundReturn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_compounding_return()
    {
        $fund = Fund::factory()->create(['starting_balance' => 1000]);

        $response = $this->postJson("/api/funds/{$fund->id}/returns", [
            'date' => '2023-01-01',
            'frequency' => 'monthly',
            'return_percentage' => 10,
            'is_compounding' => false,
        ]);

        $response->assertCreated();
        $this->assertEquals(1100, $fund->fresh()->currentValue());
    }

    public function test_compounding_return()
    {
        $fund = Fund::factory()->create(['starting_balance' => 1000]);

        $this->postJson("/api/funds/{$fund->id}/returns", [
            'date' => '2023-01-01',
            'frequency' => 'monthly',
            'return_percentage' => 10,
            'is_compounding' => true,
        ]);

        $this->postJson("/api/funds/{$fund->id}/returns", [
            'date' => '2023-02-01',
            'frequency' => 'monthly',
            'return_percentage' => 10,
            'is_compounding' => true,
        ]);

        $this->assertEquals(1210, $fund->fresh()->currentValue());
    }

    public function test_revert_return()
    {
        $fund = Fund::factory()->create(['starting_balance' => 1000]);

        $return = FundReturn::factory()->create([
            'fund_id' => $fund->id,
            'value_before' => 1000,
            'value_after' => 1100,
            'reverted' => false,
        ]);

        $response = $this->postJson("/api/returns/{$return->id}/revert");

        $response->assertOk();
        $this->assertEquals(1000, $fund->fresh()->currentValue());
        $this->assertTrue($return->fresh()->reverted);
    }

    public function test_get_value_at_date()
    {
        $fund = Fund::factory()->create(['starting_balance' => 1000]);

        FundReturn::factory()->create([
            'fund_id' => $fund->id,
            'date' => '2023-01-01',
            'value_before' => 1000,
            'value_after' => 1100,
            'reverted' => false,
        ]);

        FundReturn::factory()->create([
            'fund_id' => $fund->id,
            'date' => '2023-02-01',
            'value_before' => 1100,
            'value_after' => 1210,
            'reverted' => false,
        ]);

        $response = $this->getJson("/api/funds/{$fund->id}/value-at-date?date=2023-01-15");

        $response->assertOk()
            ->assertJson(['value' => 1100]);
    }
}