<?php

namespace Database\Factories;

use App\Models\MasterProvince;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterProvince>
 */
class MasterProvinceFactory extends Factory
{
    protected $model = MasterProvince::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'province' => $this->faker->country(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
