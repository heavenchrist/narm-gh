<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Region;
use App\Models\Training;
use App\Models\User;

class TrainingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Training::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'registration_end_date' => $this->faker->dateTime(),
            'start' => $this->faker->dateTime(),
            'end' => $this->faker->dateTime(),
            'training_mode' => $this->faker->word(),
            'status' => $this->faker->boolean(),
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => User::factory(),
            'region_id' => Region::factory(),
        ];
    }
}
