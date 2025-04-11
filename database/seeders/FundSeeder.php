<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fund;

class FundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            //

        $fund = Fund::create(
            [
            'name' => 'Volition Fund',
            'starting_balance' => 100000,
            'currency' => 'NGN',
            'description' => 'Volition Fund is a fund that invests in the Nigerian stock market.'
            ]
        );
        $this->command->info('Created Fund: ' . $fund->name);

        $fund2 = Fund::create(
            [
                'name' => 'Access Bank Fund',
                'starting_balance' => 500000,
                'currency' => 'NGN',
                'description' => 'Volition Fund is a fund that invests in the Nigerian stock market.'
            ]
            );
            $this->command->info('Created Fund: ' . $fund2->name);

        $fund3 = Fund::create(
            [
                'name' => 'NowNow Digital Fund',
                'starting_balance' => 800000,
                'currency' => 'NGN',
                'description' => 'Volition Fund is a fund that invests in the Nigerian stock market.'
            ]
        );
        $this->command->info('Created Fund: ' . $fund3->name);
    }
}
