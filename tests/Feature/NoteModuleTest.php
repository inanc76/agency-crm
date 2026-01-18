<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Note;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected User $viewer;
    protected User $otherDeptUser;
    protected Customer $customer;
    protected $department;
    protected $otherDepartment;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Reference Category for DEPARTMENT
        \App\Models\ReferenceCategory::firstOrCreate(
            ['key' => 'DEPARTMENT'],
            ['name' => 'Departmanlar', 'display_label' => 'Departmanlar', 'is_active' => true]
        );

        // 2. Create Departments
        $this->department = \App\Models\ReferenceItem::firstOrCreate(
            ['category_key' => 'DEPARTMENT', 'key' => 'SOFTWARE'],
            ['display_label' => 'Yazılım', 'is_active' => true, 'sort_order' => 1]
        );

        $this->otherDepartment = \App\Models\ReferenceItem::firstOrCreate(
            ['category_key' => 'DEPARTMENT', 'key' => 'DESIGN'],
            ['display_label' => 'Tasarım', 'is_active' => true, 'sort_order' => 2]
        );

        $this->author = User::factory()->create(['name' => 'Note Author', 'department_id' => $this->department->id]);
        $this->viewer = User::factory()->create(['name' => 'Note Viewer', 'department_id' => $this->department->id]);
        $this->otherDeptUser = User::factory()->create(['name' => 'Other Dept User', 'department_id' => $this->otherDepartment->id]);
        $this->customer = Customer::factory()->create();
    }

    public function test_it_can_create_a_note_for_customer(): void
    {
        $note = Note::create([
            'content' => 'Test customer note',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'content' => 'Test customer note',
            'entity_type' => 'CUSTOMER',
        ]);
    }

    public function test_it_can_attach_visible_departments_to_note(): void
    {
        $note = Note::create([
            'content' => 'Test note with visibility',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $note->visibleToDepartments()->attach([$this->department->id]);

        $this->assertDatabaseHas('note_department', [
            'note_id' => $note->id,
            'department_id' => $this->department->id,
        ]);
    }

    public function test_author_can_always_see_their_note(): void
    {
        $note = Note::create([
            'content' => 'Author note',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $this->assertTrue($note->canBeSeenBy($this->author));
    }

    public function test_user_in_visible_department_can_see_note(): void
    {
        $note = Note::create([
            'content' => 'Shared note with department',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $note->visibleToDepartments()->attach($this->department->id);

        $this->assertTrue($note->canBeSeenBy($this->viewer));
    }

    public function test_user_in_other_department_cannot_see_note(): void
    {
        $note = Note::create([
            'content' => 'Secret note',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $note->visibleToDepartments()->attach($this->department->id);

        $this->assertFalse($note->canBeSeenBy($this->otherDeptUser));
    }

    public function test_customer_has_notes_relationship(): void
    {
        $note = Note::create([
            'content' => 'Customer note',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $this->assertEquals(1, $this->customer->notes()->count());
        $this->assertEquals($note->id, $this->customer->notes()->first()->id);
    }
}
