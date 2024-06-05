<?php

namespace Database\Seeders;

use App\Models\DataReturDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataReturDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DataReturDetail::factory()->count(5)->create();
    }
}
