<?php

namespace App\Livewire\Projects\Traits;

use Illuminate\Validation\Rule;

/**
 * Trait HasProjectFormState
 * 
 * Manages all public properties and validation logic for Project creation/editing.
 * Separates keeping state from the component rendering logic.
 */
trait HasProjectFormState
{
    // Basic Info
    public string $name = '';
    public string $description = '';
    public string $customer_id = '';
    public string $leader_id = '';
    public ?string $status_id = null;
    public ?string $type_id = null;
    public array $team_members = [];
    public string $timezone = 'Europe/Istanbul';

    // Dates
    public ?string $start_date = null;
    public ?string $target_end_date = null;
    public ?string $completed_at = null;

    // View State
    public bool $isViewMode = false;
    public ?string $projectId = null;

    // Reference Data Containers
    public $customers = [];
    public $leaders = [];
    public $statuses = [];
    public $projectTypes = [];
    public ?array $selectedCustomer = null;

    // External User State
    public bool $inviteExternalUser = false;
    public string $externalUserEmail = '';
    public string $externalUserName = '';

    // Logic Flags
    public bool $auto_calculate_end_date = true;
    public bool $auto_calculate_start_date = true;

    /**
     * Define validation rules for the project form.
     */
    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'leader_id' => 'nullable|exists:users,id',
            'status_id' => 'required|exists:reference_items,id',
            'type_id' => 'required|exists:reference_items,id',
            'timezone' => 'required|string',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date|after_or_equal:start_date',
            'auto_calculate_end_date' => 'boolean',
            'auto_calculate_start_date' => 'boolean',
            'team_members' => 'array', // Added validation for team members
            'team_members.*' => 'exists:users,id',
        ];

        // Conditional validation for external user invite
        if ($this->inviteExternalUser) {
            // We check uniqueness inside the save logic usually to handle "if user exists already",
            // but here we can add basic structure checks.
            // The original code does conditional checks in save().
        }

        return $rules;
    }

    /**
     * Initialize empty form state
     */
    public function resetFormState(): void
    {
        $this->name = '';
        $this->description = '';
        $this->customer_id = '';
        $this->leader_id = '';
        $this->status_id = null;
        $this->type_id = null;
        $this->team_members = [];
        $this->start_date = null;
        $this->target_end_date = null;
        $this->completed_at = null;
        $this->projectId = null;
        $this->inviteExternalUser = false;
        $this->externalUserEmail = '';
        $this->externalUserName = '';
    }
}
