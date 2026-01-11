<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'number' => 'OFF-' . fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->sentence(4),
            'valid_until' => now()->addDays(30),
            'status' => 'DRAFT',
            'total_amount' => fake()->randomFloat(2, 1000, 5000),
            'currency' => 'USD',
        ];
    }
}
