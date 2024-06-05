<?php

namespace Database\Factories;

use App\Models\MasterFreqCallPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterFreqCallPlan>
 */
class MasterFreqCallPlanFactory extends Factory
{
    protected $model = MasterFreqCallPlan::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'frekuensi' => $this->faker->randomNumber(2, false),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
