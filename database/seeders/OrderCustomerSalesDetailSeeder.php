<?php

namespace Database\Seeders;

use App\Models\OrderCustomerSalesDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderCustomerSalesDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderCustomerSalesDetail::factory()->count(5)->create();
    }
}
