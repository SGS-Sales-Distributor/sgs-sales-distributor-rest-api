<?php

namespace Database\Factories;

use App\Models\MasterCallPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterCallPlan>
 */
class MasterCallPlanFactory extends Factory
{
    protected $model = MasterCallPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'month_plan' => $this->faker->month,
            'year_plan' => $this->faker->year,
            'user_id' => function () {
                return \App\Models\User::all()->random()->user_id;
            },
            'created_at' => Carbon::now(timezone: env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
            'created_by' => $this->faker->name,
        ];
    }
}
