<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\StoreInfoDistri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreInfoDistri::factory()->count(5)->create();
    }
}
