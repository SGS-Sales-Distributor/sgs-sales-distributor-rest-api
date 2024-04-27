<?php

namespace Database\Seeders;

use App\Models\MasterTargetNoo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterTargetNooSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterTargetNoo::factory()->count(5)->create();
    }
}
