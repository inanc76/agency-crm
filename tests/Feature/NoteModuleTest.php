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
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create(['name' => 'Note Author']);
        $this->viewer = User::factory()->create(['name' => 'Note Viewer']);
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

    public function test_it_can_attach_visible_users_to_note(): void
    {
        $note = Note::create([
            'content' => 'Test note with visibility',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $note->visibleTo()->attach([$this->viewer->id]);

        $this->assertDatabaseHas('note_user', [
            'note_id' => $note->id,
            'user_id' => $this->viewer->id,
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

    public function test_visible_user_can_see_note(): void
    {
        $note = Note::create([
            'content' => 'Shared note',
            'author_id' => $this->author->id,
            'entity_type' => 'CUSTOMER',
            'entity_id' => $this->customer->id,
        ]);

        $note->visibleTo()->attach($this->viewer->id);

        $this->assertTrue($note->canBeSeenBy($this->viewer));
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
