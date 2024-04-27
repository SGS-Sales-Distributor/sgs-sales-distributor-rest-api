<?php

namespace Database\Seeders;

use App\Models\MasterUserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterUserDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterUserDetail::factory()->count(5)->create();
    }
}
