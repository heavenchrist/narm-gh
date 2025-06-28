<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GeneralNotification;
use App\Models\Region;

class GeneralNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GeneralNotification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'expiry_date' => $this->faker->date(),
            'status' => $this->faker->boolean(),
            'region_id' => Region::factory(),
        ];
    }
}
