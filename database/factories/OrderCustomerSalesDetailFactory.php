<?php

namespace Database\Factories;

use App\Models\OrderCustomerSalesDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderCustomerSalesDetail>
 */
class OrderCustomerSalesDetailFactory extends Factory
{
    protected $model = OrderCustomerSalesDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'orderId' => function() {
                return \App\Models\OrderCustomerSales::all()->random()->id;
            },
            'lineNo' => $this->faker->randomNumber(2, false),
            'itemCodeCust' => function() {
                return \App\Models\ProductInfoLmt::all()->random()->prod_number;
            },
            'itemCode' => function() {
                return \App\Models\ProductInfoLmt::all()->random()->prod_number;
            },
            'qtyOrder' => $this->faker->randomNumber(3, false),
            'releaseOrder' => $this->faker->randomNumber(2, false),
            'add_disc_1' => $this->faker->randomFloat(2, 1, 100),
            'add_disc_2' => $this->faker->randomFloat(2, 1, 100),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
