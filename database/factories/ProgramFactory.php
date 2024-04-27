<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_type_program' => function() {
                return \App\Models\MasterTypeProgram::all()->random()->id_type;
            },
            'name_program' => $this->faker->word(),
            'keterangan' => $this->faker->text(),
            'active' => $this->faker->randomNumber(1, false),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
