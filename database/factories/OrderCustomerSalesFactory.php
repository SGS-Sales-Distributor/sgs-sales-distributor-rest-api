<?php

namespace Database\Factories;

use App\Models\OrderCustomerSales;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderCustomerSales>
 */
class OrderCustomerSalesFactory extends Factory
{
    protected $model = OrderCustomerSales::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_order' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'tgl_order' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
            'tipe' => $this->faker->randomElement(['SO', 'LLB']),
            'company' => $this->faker->randomNumber(2, false),
            'top' => strval($this->faker->randomNumber(2, false)),
            'cust_code' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'ship_code' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'whs_code' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'whs_code_to' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{5}'),
            'order_sts' => $this->faker->randomElement(['Open', 'Complete', 'Close', 'Draft']),
            'totOrderQty' => $this->faker->randomNumber(3, false),
            'totReleaseQty' => $this->faker->randomNumber(3, false),
            'keterangan' => $this->faker->text(),
            'llb_gabungan_reff' => $this->faker->word(),
            'llb_gabungan_sts' => $this->faker->randomElement(['Open', 'Transfered']),
            'uploaded_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            'uploaded_by' => $this->faker->name(),
            'store_id' => function() {
                return \App\Models\StoreInfoDistri::all()->random()->store_id;
            },
            'status_id' => function() {
                return \App\Models\MasterStatus::all()->random()->id;
            },
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
