<?php

namespace Database\Seeders;

use App\Models\ProductInfoLmt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductInfoLmtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductInfoLmt::factory()->count(5)->create();
    }
}
