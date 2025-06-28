<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Office;
use App\Models\Region;
use App\Models\Report;
use App\Models\User;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'report_url' => $this->faker->text(),
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
            'region_id' => Region::factory(),
            'received_by' => User::factory()->create()->received_by,
            'is_received' => $this->faker->boolean(),
            'received_date' => $this->faker->dateTime(),
            'remarks' => $this->faker->text(),
        ];
    }
}
