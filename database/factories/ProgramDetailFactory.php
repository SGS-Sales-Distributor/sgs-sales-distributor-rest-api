<?php

namespace Database\Factories;

use App\Models\ProgramDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramDetail>
 */
class ProgramDetailFactory extends Factory
{
    protected $model = ProgramDetail::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_program' => function() {
                return \App\Models\Program::all()->random()->id;
            },
            'condition' => $this->faker->randomElement(['running', 'not active', 'on working']),
            'get' => $this->faker->word(),
            'product' => $this->faker->word(),
            'qty' => $this->faker->randomNumber(2, false),
            'disc_val' => $this->faker->randomFloat(2, 10, 50),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
