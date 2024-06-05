<?php

namespace Database\Seeders;

use App\Models\MasterTypeProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterTypeProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterTypeProgram::factory()->count(5)->create();
    }
}
