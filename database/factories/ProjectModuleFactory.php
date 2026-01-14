<?php

namespace Database\Factories;

use App\Models\ProjectModule;
use App\Models\ProjectPhase;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectModuleFactory extends Factory
{
    protected $model = ProjectModule::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');

        return [
            'phase_id' => ProjectPhase::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+3 months'),
            'status_id' => fn () => ReferenceItem::where('category_key', 'MODULE_STATUS')
                ->where('is_default', true)->value('id'),
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'MODULE_STATUS')
                ->where('key', 'module_in_progress')->value('id'),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'MODULE_STATUS')
                ->where('key', 'module_paused')->value('id'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'MODULE_STATUS')
                ->where('key', 'module_completed')->value('id'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => ReferenceItem::where('category_key', 'MODULE_STATUS')
                ->where('key', 'module_cancelled')->value('id'),
        ]);
    }
}
