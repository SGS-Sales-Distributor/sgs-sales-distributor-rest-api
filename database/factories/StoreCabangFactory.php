<?php

namespace Database\Factories;

use App\Models\StoreCabang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreCabang>
 */
class StoreCabangFactory extends Factory
{
    protected $model = StoreCabang::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'province_id' => function() {
                return \App\Models\MasterProvince::all()->random()->id_province;
            },
            'kode_cabang' => $this->faker->regexify('[A-Z]{5}[0-9]{5}'),
            'nama_cabang' => $this->faker->citySuffix(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
