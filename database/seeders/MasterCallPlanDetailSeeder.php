<?php

namespace Database\Seeders;

use App\Models\MasterCallPlanDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterCallPlanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterCallPlanDetail::factory()->count(5)->create();
    }
}
