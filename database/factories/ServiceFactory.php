<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\PriceDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'asset_id' => Asset::factory(),
            'price_definition_id' => PriceDefinition::factory(),
            'service_name' => fake()->sentence(3),
            'service_category' => 'HOSTING',
            'service_duration' => 'YEARLY',
            'service_price' => fake()->randomFloat(2, 50, 1000),
            'service_currency' => 'USD',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'ACTIVE',
        ];
    }
}
