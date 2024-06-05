<?php

namespace Database\Factories;

use App\Models\ProductInfoDo;
use App\Models\ProductInfoLmt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductInfoLmt>
 */
class ProductInfoLmtFactory extends Factory
{
    protected $model = ProductInfoLmt::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prod_number' => ProductInfoDo::factory(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
