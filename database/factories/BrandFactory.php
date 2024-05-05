<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'brand_name' => $this->faker->word(),
            'status' => $this->faker->randomElement([0, 1]),
            'brand_group_id' => function() {
                return \App\Models\BrandGroup::all()->random()->brand_group_id;
            },
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
