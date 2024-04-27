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
            'user_number' => $this->faker->regexify('[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{2}'),
            'user_nik' => $this->faker->unique()->numerify('####-####-####-####'),
            'user_fullname' => $this->faker->name(),
            'user_phone' => $this->faker->phoneNumber(),
            'user_email' => $this->faker->unique()->safeEmail(),
            'user_name' => $this->faker->unique()->word(),
            'user_password' => Hash::make('password'),
            'user_type_id' => function () {
                return \App\Models\UserType::all()->random()->user_type_id;
            },
            'user_status' => function () {
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
