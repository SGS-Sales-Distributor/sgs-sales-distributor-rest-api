<?php

namespace Database\Factories;

use App\Models\ProductInfoDo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductInfoDo>
 */
class ProductInfoDoFactory extends Factory
{
    protected $model = ProductInfoDo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prod_number' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'prod_barcode_number' => $this->faker->ean13(),
            'prod_universal_number' => $this->faker->regexify('[A-Z]{3}[0-9]{2}[A-Z]{5}'),
            'prod_name' => $this->faker->name(),
            'prod_base_price' => number_format($this->faker->randomFloat(2, 10_000, 1_000_000), 2),
            'prod_unit_price' => number_format($this->faker->randomFloat(2, 10_000, 1_000_000), 2),
            'prod_promo_price' => number_format($this->faker->randomFloat(2, 10_000, 1_000_000), 2),
            'prod_special_offer' => strval($this->faker->randomNumber(2, false)),
            'prod_special_offer_unit' => strval($this->faker->randomNumber(2, false)),
            'brand_id' => function() {
                return \App\Models\Brand::all()->random()->brand_id;
            },
            'category_id' => strval($this->faker->randomNumber(2, false)),
            'category_sub_id' => $this->faker->randomNumber(2, false),
            'prod_type_id' => function() {
                return \App\Models\ProductType::all()->random()->prod_type_id;
            },
            'supplier_id' => strval($this->faker->randomNumber(2, false)),
            'prod_status_id' => function() {
                return \App\Models\ProductStatus::all()->random()->product_status_id;
            },
            'status_aktif' => strval($this->faker->randomNumber(2, false)),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
