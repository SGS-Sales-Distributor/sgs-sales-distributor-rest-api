<?php

namespace Database\Seeders;

use App\Models\ProgramDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramDetail::factory()->count(5)->create();
    }
}
