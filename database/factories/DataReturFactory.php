<?php

namespace Database\Factories;

use App\Models\DataRetur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataRetur>
 */
class DataReturFactory extends Factory
{
    protected $model = DataRetur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'custmrCode' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'custmrName' => $this->faker->name(),
            'shiptoCode' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'termCode' => $this->faker->unique()->regexify('[A-Z]{5}'),
            'whsCode' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'whsCodeTo' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'refference' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'comments' => $this->faker->text(),
            'docDate' => $this->faker->date(),
            'proccessDate' => $this->faker->dateTime(),
            'transferSts' => $this->faker->randomElement(['0', '1']),
            'companyId' => $this->faker->randomNumber(2, false),
            'trxId' => $this->faker->randomNumber(2, false),
            'isllb' => $this->faker->randomNumber(2, false),
            'docEntryGR1' => $this->faker->randomNumber(2, false),
            'docEntryGI' => $this->faker->randomNumber(2, false),
            'docEntryGR2' => $this->faker->randomNumber(2, false),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
