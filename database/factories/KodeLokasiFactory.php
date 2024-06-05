<?php

namespace Database\Factories;

use App\Models\KodeLokasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KodeLokasi>
 */
class KodeLokasiFactory extends Factory
{
    protected $model = KodeLokasi::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_cabang' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'nama_cabang' => $this->faker->country(),
            'kode_lokasi' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
