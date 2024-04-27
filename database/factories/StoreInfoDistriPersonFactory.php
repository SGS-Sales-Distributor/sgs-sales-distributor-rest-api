<?php

namespace Database\Factories;

use App\Models\StoreInfoDistriPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreInfoDistriPerson>
 */
class StoreInfoDistriPersonFactory extends Factory
{
    protected $model = StoreInfoDistriPerson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => function() {
                return \App\Models\StoreInfoDistri::all()->random()->store_id;
            },
            'owner' => $this->faker->name(),
            'nik_owner' => $this->faker->unique()->numerify('####-####-####-####'),
            'email_owner' => $this->faker->unique()->safeEmail(),
            'ktp_owner' => $this->faker->image(
                width: 640,
                height: 480,
                word: 'testing',
            ),
            'photo_other' => $this->faker->image(
                width: 640,
                height: 480,
                word: 'testing',
            ),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
