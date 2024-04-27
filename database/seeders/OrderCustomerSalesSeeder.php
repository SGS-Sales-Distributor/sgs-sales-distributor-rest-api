<?php

namespace Database\Seeders;

use App\Models\OrderCustomerSales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderCustomerSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderCustomerSales::factory()->count(5)->create();
    }
}
