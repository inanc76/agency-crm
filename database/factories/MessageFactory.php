<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'customer_id' => Customer::factory(),
            'subject' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'type' => 'EMAIL',
            'status' => 'SENT',
            'sent_at' => now(),
        ];
    }
}
