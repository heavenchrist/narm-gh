<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;

class TrainingRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrainingRegistration::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'member_id' => User::factory(),
            'training_id' => Training::factory(),
            'attended' => $this->faker->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
