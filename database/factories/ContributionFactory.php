<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Contribution;
use App\Models\User;

class ContributionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contribution::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'staff_id' => $this->faker->word(),
            'place_of_work' => $this->faker->word(),
            'district' => $this->faker->word(),
            'region' => $this->faker->word(),
            'period' => $this->faker->word(),
            'amount' => $this->faker->numberBetween(-10000, 10000),
            'staff_id_id' => User::factory(),
        ];
    }
}
