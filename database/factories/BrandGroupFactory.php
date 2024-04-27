<?php

namespace Database\Factories;

use App\Models\BrandGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BrandGroup>
 */
class BrandGroupFactory extends Factory
{
    protected $model = BrandGroup::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_group_id' => $this->faker->regexify('[A-Z]{5}[0-9]{5}'),
            'brand_group_name' => $this->faker->word(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
