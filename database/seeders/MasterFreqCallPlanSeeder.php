<?php

namespace Database\Seeders;

use App\Models\MasterFreqCallPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterFreqCallPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterFreqCallPlan::factory()->count(5)->create();
    }
}
