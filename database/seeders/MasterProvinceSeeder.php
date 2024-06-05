<?php

namespace Database\Seeders;

use App\Models\MasterProvince;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterProvince::factory()->count(5)->create();
    }
}
