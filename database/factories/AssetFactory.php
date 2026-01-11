<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'customer_id' => Customer::factory(),
            'name' => fake()->domainName(),
            'type' => 'DOMAIN',
            'url' => fake()->url(),
        ];
    }
}
