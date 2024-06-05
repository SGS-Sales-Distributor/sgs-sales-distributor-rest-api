<?php

namespace Database\Seeders;

use App\Models\MasterUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterUser::factory()->count(5)->create();
    }
}
