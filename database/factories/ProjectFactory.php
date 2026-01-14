<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'leader_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'timezone' => 'Europe/Istanbul',
            'status_id' => fn () => ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('is_default', true)->value('id'),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'target_end_date' => fake()->dateTimeBetween('+2 months', '+6 months'),
            'custom_fields' => [],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('key', 'project_active')->value('id'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('key', 'project_completed')->value('id'),
        ]);
    }
}
