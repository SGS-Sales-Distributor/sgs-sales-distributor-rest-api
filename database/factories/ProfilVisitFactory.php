<?php

namespace Database\Factories;

use App\Models\ProfilVisit;
use App\Models\StoreInfoDistri;
use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\CssSelector\Node\FunctionNode;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfilVisit>
 */
class ProfilVisitFactory extends Factory
{
    protected $model = ProfilVisit::class;

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
            'user' => function() {
                return \App\Models\User::all()->random()->user_fullname;
            },
            'photo_visit' => $this->faker->image(
                width: 640,
                height: 480,
                word: 'photo visit',
            ),
            'photo_visit_out' => $this->faker->image(
                width: 640,
                height: 480,
                word: 'photo visit out',
            ),
            'tanggal_visit' => $this->faker->date('Y-m-d'),
            'time_in' => $this->faker->time('H:i:s'),
            'time_out' => $this->faker->time('H:i:s'),
            'purchase_order_in' => $this->faker->numberBetween(0, 2),
            'condit_owner' => $this->faker->randomElement(['Tidak Ada', 'Ada']),
            'ket' => $this->faker->text(),
            'comment_appr' => $this->faker->text(),
            'lat_in' => $this->faker->latitude(),
            'long_in' => $this->faker->longitude(),
            'lat_out' => $this->faker->latitude(),
            'long_out' => $this->faker->longitude(),
            'created_by' => $this->faker->name(),
            'updated_by' => $this->faker->name(),
        ];
    }
}
