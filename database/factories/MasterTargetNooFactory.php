<?php

namespace Database\Factories;

use App\Models\MasterTargetNoo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterTargetNoo>
 */
class MasterTargetNooFactory extends Factory
{
    protected $model = MasterTargetNoo::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usernumber' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'target' => $this->faker->randomNumber(2, false),
            'month' => $this->faker->month(),
            'year' => $this->faker->year(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
