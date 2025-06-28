<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ExecutivePosition;
use App\Models\Office;
use App\Models\OfficeExecutive;

class OfficeExecutiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficeExecutive::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'office_id' => Office::factory(),
            'executive_position_id' => ExecutivePosition::factory(),
            'telephone' => $this->faker->word(),
            'status' => $this->faker->boolean(),
        ];
    }
}
