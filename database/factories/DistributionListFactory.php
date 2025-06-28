<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\DistributionList;
use App\Models\Member;
use App\Models\Office;
use App\Models\User;

class DistributionListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DistributionList::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'distribution_id' => Distribution::factory(),
            'is_received' => $this->faker->boolean(),
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
            'distribution_item_id' => DistributionItem::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'distribution_list_id' => DistributionList::factory(),
        ];
    }
}
