<?php

namespace Database\Factories;

use App\Models\ProjectModule;
use App\Models\ProjectTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectTaskFactory extends Factory
{
    protected $model = ProjectTask::class;

    public function definition(): array
    {
        return [
            'module_id' => ProjectModule::factory(),
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status_id' => null, // Task için henüz TASK_STATUS kategorisi yok
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => fake()->dateTimeBetween('now', '+2 months'),
            'estimated_hours' => fake()->randomFloat(2, 1, 40),
            'actual_hours' => null,
            'order' => fake()->numberBetween(0, 20),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'actual_hours' => fake()->randomFloat(2, 0.5, 10),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'actual_hours' => fake()->randomFloat(2, 1, 40),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
            'due_date' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }
}
