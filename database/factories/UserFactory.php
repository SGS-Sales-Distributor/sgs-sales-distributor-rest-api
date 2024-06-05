<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->regexify('[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}'),
            'nik' => $this->faker->unique()->numerify('####-####-####-####'),
            'fullname' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->unique()->word(),
            'password' => Hash::make('password'),
            'type_id' => function () {
                return \App\Models\UserType::all()->random()->user_type_id;
            },
            'status' => function () {
                return \App\Models\UserStatus::all()->random()->id;
            },
            'cabang_id' => $this->faker->randomNumber(2, false),
            'store_id' => $this->faker->randomNumber(2, false),
            'status_ba' => $this->faker->numerify('##'),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
