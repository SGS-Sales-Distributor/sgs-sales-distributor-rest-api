<?php

namespace Database\Seeders;

use App\Models\ProductInfoDo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductInfoDoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductInfoDo::factory()->count(5)->create();
    }
}
