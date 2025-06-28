<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Renewal;
use App\Models\User;

class RenewalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Renewal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'pin_ain' => $this->faker->word(),
            'registration_number' => $this->faker->word(),
            'staff_id' => $this->faker->word(),
            'renewal_date' => $this->faker->date(),
            'expiry_date' => $this->faker->date(),
            'period' => $this->faker->numberBetween(-10000, 10000),
            'staff_id_id' => User::factory(),
        ];
    }
}
