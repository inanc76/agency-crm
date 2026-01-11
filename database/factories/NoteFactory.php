<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
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
            'content' => fake()->paragraph(),
            'author_id' => User::factory(),
            'entity_type' => 'CUSTOMER',
            'entity_id' => fake()->uuid(), // Will be overridden by relationship
        ];
    }
}
