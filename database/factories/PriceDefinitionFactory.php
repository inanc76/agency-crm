<?php

namespace Database\Factories;

use App\Models\PriceDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceDefinitionFactory extends Factory
{
    protected $model = PriceDefinition::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->words(3, true),
            'category' => 'HOSTING',
            'duration' => 'YEARLY',
            'price' => fake()->randomFloat(2, 100, 5000),
            'currency' => 'USD',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
