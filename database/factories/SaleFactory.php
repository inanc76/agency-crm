<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'offer_id' => Offer::factory(), // İlişkili offer
            'amount' => fake()->randomFloat(2, 100, 10000),
            'currency' => 'USD',
            'sale_date' => now(),
        ];
    }
}
