<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectPhaseFactory extends Factory
{
    protected $model = ProjectPhase::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'status_id' => fn () => ReferenceItem::where('category_key', 'PHASE_STATUS')
                ->where('is_default', true)->value('id'),
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'PHASE_STATUS')
                ->where('key', 'phase_in_progress')->value('id'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'PHASE_STATUS')
                ->where('key', 'phase_completed')->value('id'),
        ]);
    }
}
