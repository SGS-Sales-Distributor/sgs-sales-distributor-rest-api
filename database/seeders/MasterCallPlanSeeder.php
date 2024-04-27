<?php

namespace Database\Seeders;

use App\Models\MasterCallPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterCallPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterCallPlan::factory()->count(5)->create();
    }
}
