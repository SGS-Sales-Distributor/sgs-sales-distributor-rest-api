<?php

namespace Database\Factories;

use App\Models\StoreInfoDistri;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreInfoDistriFactory extends Factory
{
    protected $model = StoreInfoDistri::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_name' => $this->faker->company . $this->faker->companySuffix,
            'store_alias' => $this->faker->company,
            'store_address' => $this->faker->address,
            'store_phone' => $this->faker->phoneNumber,
            'store_fax' => $this->faker->e164PhoneNumber,
            'store_type_id' => function() {
                return \App\Models\StoreType::all()->random()->store_type_id;
            },
            'subcabang_id' => function() {
                return \App\Models\StoreCabang::all()->random()->id;
            },
            'store_code' => $this->faker->numerify('OS#-####'),
            'active' => $this->faker->randomElement([0, 1]),
            'subcabang_idnew' => function() {
                return \App\Models\StoreCabang::all()->random()->id;
            },
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
