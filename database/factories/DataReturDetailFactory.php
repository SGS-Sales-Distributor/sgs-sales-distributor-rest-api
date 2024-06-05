<?php

namespace Database\Factories;

use App\Models\DataReturDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataReturDetail>
 */
class DataReturDetailFactory extends Factory
{
    protected $model = DataReturDetail::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'baseId' => function() {
                return \App\Models\DataRetur::all()->random()->id;
            },
            'lineNo' => $this->faker->randomNumber(2, false),
            'itemCodeBase' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'itemCode' => function() {
                return \App\Models\ProductInfoDo::all()->random()->prod_number;
            },
            'quantity' => $this->faker->randomNumber(3, false),
            'disc2' => $this->faker->randomFloat(2, 1, 100),
            'disc3' => $this->faker->randomFloat(2, 1, 100),
            'batchNo' => strval($this->faker->randomNumber(2, false)),
            'expireDate' => $this->faker->date(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
