<?php

namespace Database\Seeders;

use App\Models\KodeLokasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KodeLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KodeLokasi::factory()->count(5)->create();
    }
}
