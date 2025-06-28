<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Office;
use App\Models\Region;

class OfficeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Office::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'digital_address' => $this->faker->word(),
            'location' => $this->faker->word(),
            'region' => $this->faker->word(),
            'status' => $this->faker->boolean(),
            'region_id' => Region::factory(),
        ];
    }
}
