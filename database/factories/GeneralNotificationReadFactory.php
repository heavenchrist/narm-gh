<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GeneralNotification;
use App\Models\GeneralNotificationRead;
use App\Models\User;

class GeneralNotificationReadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GeneralNotificationRead::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'member_id' => $this->faker->word(),
            'office_id' => $this->faker->word(),
            'general_notification_id' => GeneralNotification::factory(),
            'user_id' => User::factory(),
        ];
    }
}
