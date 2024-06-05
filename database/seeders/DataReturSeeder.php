<?php

namespace Database\Seeders;

use App\Models\DataRetur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataReturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DataRetur::factory()->count(5)->create();
    }
}
