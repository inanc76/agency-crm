# Notes Module Specification

## Overview
A polymorphic notes system that allows users to attach notes to multiple entity types across the CRM application with granular visibility control.

## Status
✅ **COMPLETED** - All requirements implemented and tested

## User Stories

### US-1: Add Notes to Multiple Entity Types
**As a** CRM user  
**I want to** add notes to various entities (projects, tasks, customers, contacts, assets, services, offers)  
**So that** I can keep contextual information attached to relevant records

**Acceptance Criteria:**
- ✅ Notes can be attached to 7 entity types: Project, ProjectTask, Customer, Contact, Asset, Service, Offer
- ✅ Each entity detail page has a "Notlar" (Notes) tab
- ✅ Notes tab displays all notes related to that entity
- ✅ "Yeni Not" button opens a modal for creating new notes
- ✅ Button uses `theme-btn-save` CSS class

### US-2: Note Visibility Control
**As a** CRM user  
**I want to** control who can see each note  
**So that** sensitive information is only visible to authorized users

**Acceptance Criteria:**
- ✅ Each note has a "Kimler Görebilir" (Who Can See) field
- ✅ Multiple users can be selected to view a note
- ✅ Uses many-to-many relationship via `note_user` pivot table
- ✅ Only authorized users see notes in the list

### US-3: Automatic Note Metadata
**As a** CRM user  
**I want** note creation details to be captured automatically  
**So that** I know when and by whom notes were created

**Acceptance Criteria:**
- ✅ Creation date is automatically recorded
- ✅ Creator user is automatically recorded
- ✅ Both fields are displayed in the note list

### US-4: Shared Component Architecture
**As a** developer  
**I want** a single reusable notes component  
**So that** maintenance is easier and UI is consistent

**Acceptance Criteria:**
- ✅ Single shared Livewire component: `shared/notes-tab.blade.php`
- ✅ Single modal file: `_modal-note.blade.php`
- ✅ Single trait: `HasNoteActions` for CRUD operations
- ✅ All entity pages use the same component with different parameters

## Technical Requirements

### Database Schema

#### Notes Table
```sql
- id (uuid, primary key)
- notable_type (string) - polymorphic type
- notable_id (uuid) - polymorphic id
- user_id (uuid) - creator
- content (text) - note content
- created_at (timestamp)
- updated_at (timestamp)
```

#### Note-User Pivot Table
```sql
- note_id (uuid, foreign key)
- user_id (uuid, foreign key)
- Primary key: (note_id, user_id)
```

### Models

#### Note Model
- ✅ Polymorphic relationship: `notable()`
- ✅ Belongs to User: `user()`
- ✅ Many-to-many with User: `visibleToUsers()`
- ✅ Methods: `isVisibleTo($user)`, `canBeEditedBy($user)`

#### Entity Models
All 7 entity models have:
- ✅ `notes()` morphMany relationship

### Components

#### Shared Notes Tab Component
**Path:** `resources/views/livewire/shared/notes-tab.blade.php`

**Properties:**
- `entityType` (string) - The entity class name
- `entityId` (string) - The entity UUID
- `notes` (collection) - Filtered notes visible to current user

**Features:**
- ✅ Lists all visible notes
- ✅ "Yeni Not" button to open modal
- ✅ Edit/Delete actions for note creator
- ✅ Real-time updates via Livewire

#### HasNoteActions Trait
**Path:** `app/Livewire/Traits/HasNoteActions.php`

**Methods:**
- ✅ `openNoteModal()` - Opens create modal
- ✅ `editNote($noteId)` - Opens edit modal
- ✅ `saveNote()` - Creates/updates note
- ✅ `deleteNote($noteId)` - Deletes note
- ✅ `loadNotes()` - Fetches visible notes

### UI/UX Requirements

#### Notes Tab
- ✅ Displays in tab navigation on entity detail pages
- ✅ Shows list of notes with:
  - Creator name and avatar
  - Creation date (formatted)
  - Note content
  - Edit/Delete buttons (for creator only)

#### Note Modal
- ✅ Title: "Yeni Not" (create) or "Notu Düzenle" (edit)
- ✅ Fields:
  - Content (textarea, required)
  - Visible To Users (multi-select, required)
- ✅ Actions:
  - Save button (`theme-btn-save`)
  - Cancel button

#### Layout Changes
- ✅ Contact detail: Removed right column photo card, full width layout
- ✅ Asset detail: Removed right column photo card, full width layout
- ✅ Service detail: Removed right column photo card, full width layout
- ✅ Customer detail: Removed logo card from sidebar, full width layout

## Integration Points

### Entity Pages with Notes Tab

1. **Project Detail**
   - URL: `/dashboard/projects/{id}?tab=notes`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Project', 'entityId' => $project->id])`

2. **Project Task Detail**
   - URL: `/dashboard/projects/tasks/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\ProjectTask', 'entityId' => $task->id])`

3. **Customer Detail**
   - URL: `/dashboard/customers/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Customer', 'entityId' => $customer->id])`

4. **Contact Detail**
   - URL: `/dashboard/customers/contacts/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Contact', 'entityId' => $contact->id])`

5. **Asset Detail**
   - URL: `/dashboard/customers/assets/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Asset', 'entityId' => $asset->id])`

6. **Service Detail**
   - URL: `/dashboard/customers/services/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Service', 'entityId' => $service->id])`

7. **Offer Detail**
   - URL: `/dashboard/customers/offers/{id}`
   - Component: `@livewire('shared.notes-tab', ['entityType' => 'App\\Models\\Offer', 'entityId' => $offer->id])`

## Testing

### Test Coverage
✅ **File:** `tests/Feature/NoteModuleTest.php`

**Test Cases:**
1. ✅ User can create a note with visibility settings
2. ✅ User can only see notes they have permission to view
3. ✅ User can edit their own notes
4. ✅ User cannot edit other users' notes
5. ✅ User can delete their own notes

**Results:** 5 tests, 6 assertions - All passing

## Documentation

### Created Documentation Files
1. ✅ `docs/notes-module-quick-start.md` - Quick start guide
2. ✅ `docs/notes-module-integration.md` - Integration guide
3. ✅ `NOTLAR_MODULU_OZET.md` - Turkish summary
4. ✅ `NOTLAR_MODULU_DUZELTMELER.md` - Bug fixes log

## Implementation Files

### Backend
- ✅ `app/Models/Note.php` - Note model with relationships
- ✅ `app/Livewire/Traits/HasNoteActions.php` - Shared CRUD logic
- ✅ `database/migrations/2024_01_01_000017_create_note_user_table.php` - Pivot table migration

### Frontend
- ✅ `resources/views/livewire/shared/notes-tab.blade.php` - Main component
- ✅ `resources/views/livewire/shared/notes/partials/_notes-actions.blade.php` - Action buttons
- ✅ `resources/views/livewire/shared/notes/partials/_notes-list.blade.php` - Notes list
- ✅ `resources/views/livewire/shared/notes/partials/_modal-note.blade.php` - Create/Edit modal

### Entity Models Updated
- ✅ `app/Models/Project.php`
- ✅ `app/Models/ProjectTask.php`
- ✅ `app/Models/Customer.php`
- ✅ `app/Models/Contact.php`
- ✅ `app/Models/Asset.php`
- ✅ `app/Models/Service.php`
- ✅ `app/Models/Offer.php`

### Entity Views Updated
- ✅ `resources/views/livewire/projects/edit.blade.php`
- ✅ `resources/views/livewire/customers/create.blade.php`
- ✅ `resources/views/livewire/modals/contact-form.blade.php`
- ✅ `resources/views/livewire/modals/asset-form.blade.php`
- ✅ `resources/views/livewire/modals/service-form.blade.php`

## Future Enhancements

### Potential Features (Not in Current Scope)
- [ ] Note attachments (files, images)
- [ ] Note categories/tags
- [ ] Note search functionality
- [ ] Note activity log (edit history)
- [ ] Email notifications for note mentions
- [ ] Rich text editor for note content
- [ ] Note templates
- [ ] Bulk note operations

## Lessons Learned

### What Worked Well
1. **Polymorphic relationships** - Clean way to attach notes to multiple entity types
2. **Shared component architecture** - Single source of truth, easy maintenance
3. **Trait-based logic** - Reusable CRUD operations across components
4. **Visibility control** - Flexible permission system using pivot table

### Challenges Overcome
1. **Variable scope issues** - Fixed by using explicit Livewire parameters instead of includes
2. **Layout inconsistencies** - Standardized by removing photo cards and using full-width layouts
3. **Button styling** - Unified by using `theme-btn-save` class across all pages

## Approval & Sign-off

**Specification Created:** 2026-01-16  
**Implementation Completed:** 2026-01-16  
**Tests Passing:** ✅ Yes (5/5)  
**Documentation Complete:** ✅ Yes  
**Ready for Production:** ✅ Yes

---

## Appendix

### Related Documents
- [Quick Start Guide](../docs/notes-module-quick-start.md)
- [Integration Guide](../docs/notes-module-integration.md)
- [Turkish Summary](../NOTLAR_MODULU_OZET.md)
- [Bug Fixes Log](../NOTLAR_MODULU_DUZELTMELER.md)

### Database Diagram
```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   notes     │         │  note_user   │         │    users    │
├─────────────┤         ├──────────────┤         ├─────────────┤
│ id          │────┐    │ note_id (FK) │    ┌────│ id          │
│ notable_type│    └───→│ user_id (FK) │←───┘    │ name        │
│ notable_id  │         └──────────────┘         │ email       │
│ user_id (FK)│                                   └─────────────┘
│ content     │
│ created_at  │
└─────────────┘
      ↑
      │ (polymorphic)
      │
┌─────┴──────────────────────────────────────┐
│                                             │
│  Project, ProjectTask, Customer, Contact,  │
│  Asset, Service, Offer                     │
│                                             │
└─────────────────────────────────────────────┘
```
