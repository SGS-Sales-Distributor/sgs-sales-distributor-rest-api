<?php

namespace Database\Factories;

use App\Models\MasterUserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterUserDetail>
 */
class MasterUserDetailFactory extends Factory
{
    protected $model = MasterUserDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user' => function() {
                return \App\Models\MasterUser::all()->random()->user;
            },
            'groupcode' => $this->faker->regexify('[A-Z]{5}[0-9]{5}'),
            'entryuser' => $this->faker->name(),
            'entryip' => $this->faker->ipv4(),
            'updateuser' => $this->faker->name(),
            'updateip' => $this->faker->ipv4(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
