<?php

namespace Database\Seeders;

use App\Models\StoreInfoDistriPerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreInfoDistriPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreInfoDistriPerson::factory()->count(5)->create();
    }
}
