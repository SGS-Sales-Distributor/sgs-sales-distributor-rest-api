<?php

namespace Database\Seeders;

use App\Models\BrandGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrandGroup::factory()->count(5)->create();
    }
}
