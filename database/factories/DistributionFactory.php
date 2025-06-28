<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\DistributionList;
use App\Models\Office;
use App\Models\User;

class DistributionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Distribution::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'distribution_item_id' => DistributionItem::factory(),
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'status' => $this->faker->boolean(),
            'remarks' => $this->faker->text(),
            'distribution_list_id' => DistributionList::factory(),
        ];
    }
}
