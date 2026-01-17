<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectReportFactory extends Factory
{
    protected $model = ProjectReport::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'report_type' => 'PROJECT',
            'project_id' => Project::factory(),
            'hours' => $this->faker->numberBetween(1, 8),
            'minutes' => $this->faker->randomElement([0, 15, 30, 45]),
            'content' => $this->faker->paragraph(),
            'created_by' => User::factory(),
        ];
    }
}
