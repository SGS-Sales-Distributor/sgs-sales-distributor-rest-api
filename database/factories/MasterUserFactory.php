<?php

namespace Database\Factories;

use App\Models\MasterUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterUser>
 */
class MasterUserFactory extends Factory
{
    protected $model = MasterUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'user' => $this->faker->name(),
            'description' => $this->faker->text(),
            'password' => Hash::make('password'),
            'username' => $this->faker->word(),
            'defaultpassword' => 'password',
            'nik' => $this->faker->unique()->numerify('####-####-####-####'),
            'departmentId' => $this->faker->randomNumber(2, false),
            'unitId' => $this->faker->randomNumber(2, false),
            'entryuser' => $this->faker->name(),
            'entryip' => $this->faker->ipv4(),
            'updateuser' => $this->faker->name(),
            'updateip' => $this->faker->ipv4(),
            'avatar' => $this->faker->image(
                width: 640,
                height: 480,
                word: 'testing',
            ),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
