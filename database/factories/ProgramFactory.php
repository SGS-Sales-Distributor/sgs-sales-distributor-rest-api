<?php

namespace Database\Factories;

use App\Models\Program;
use Carbon\Carbon;
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
            'active' => $this->faker->randomElement([0, 1]),
            'periode_start' => $this->faker->date(),
            'periode_end' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
