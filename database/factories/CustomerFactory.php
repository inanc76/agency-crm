<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'customer_type' => 'CUSTOMER',
            // 'city_id' => '34',
            // 'country_id' => 'TR',
            'emails' => [fake()->email()],
            'phones' => [fake()->phoneNumber()],
            'websites' => [fake()->url()],
        ];
    }
}
