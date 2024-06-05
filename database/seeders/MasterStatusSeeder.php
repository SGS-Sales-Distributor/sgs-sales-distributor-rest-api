<?php

namespace Database\Seeders;

use App\Models\MasterStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterStatus::factory()->count(5)->create();
    }
}
