<?php

namespace Database\Factories;

use App\Models\MasterCallPlan;
use App\Models\MasterCallPlanDetail;
use App\Models\StoreInfoDistri;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterCallPlanDetail>
 */
class MasterCallPlanDetailFactory extends Factory
{
    protected $model = MasterCallPlanDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'call_plan_id' => function() {
                return \App\Models\MasterCallPlan::all()->random()->id;
            },
            'store_id' => function() {
                return \App\Models\StoreInfoDistri::all()->random()->store_id;
            },
            'date' => $this->faker->date(format: 'Y-m-d'),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
