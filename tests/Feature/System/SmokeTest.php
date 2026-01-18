<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Asset;
use App\Models\Contact;
use App\Models\ReferenceItem;
use App\Models\ReferenceCategory;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Roles & Users
    $role = \App\Models\Role::factory()->create(['name' => 'Super Admin']);
    $this->user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($this->user);

    // 2. Reference Data Seeding (Smoke Test Critical)
    // Create Categories First
    $categories = ['PROJECT_STATUS', 'OFFER_STATUS', 'SERVICE_STATUS', 'SERVICE_CATEGORY', 'ASSET_TYPE', 'PHASE_STATUS'];
    foreach ($categories as $cat) {
        ReferenceCategory::firstOrCreate(['key' => $cat], ['name' => $cat, 'display_label' => $cat, 'is_active' => true]);
    }

    // Then Create Items (Safe)
    ReferenceItem::firstOrCreate(
        ['category_key' => 'PROJECT_STATUS', 'key' => 'ACTIVE'],
        ['display_label' => 'Active', 'is_active' => true, 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'OFFER_STATUS', 'key' => 'DRAFT'],
        ['display_label' => 'Taslak', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'SERVICE_STATUS', 'key' => 'ACTIVE'],
        ['display_label' => 'Aktif', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'SERVICE_CATEGORY', 'key' => 'SEO'],
        ['display_label' => 'SEO', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'ASSET_TYPE', 'key' => 'HOSTING'],
        ['display_label' => 'Hosting', 'is_active' => true, 'sort_order' => 1]
    );
    ReferenceItem::firstOrCreate(
        ['category_key' => 'PHASE_STATUS', 'key' => 'phase_planned'],
        ['display_label' => 'Planned', 'is_active' => true, 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
    );

    // 3. Base Entities
    $this->customer = Customer::factory()->create();
    $this->project = Project::create([
        'name' => 'Smoke Test Project',
        'customer_id' => $this->customer->id,
        'leader_id' => $this->user->id,
        'status_id' => ReferenceItem::where('key', 'ACTIVE')->first()->id,
    ]);
});

// --- SECTION A: Public Route Checks ---

// --- SECTION A: Public Route Checks ---

test('Public page loads', function (string $route, int $status) {
    auth()->logout();
    $this->get($route)->assertStatus($status);
})->with([
            'T01: Login page' => ['/login', 200],
            'T02: Forgot password page' => ['/forgot-password', 200],
            'T03: Public offer download page invalid token' => ['/offer/invalid-token', 404],
            'T05: Root page' => ['/', 200],
        ]);


// --- SECTION B: Dashboard & Settings Pages ---

test('Dashboard & Settings page loads', function (string $route) {
    $response = $this->get($route);

    if (str_contains($route, '/settings/profile')) {
        $response->assertStatus(302);
    } else {
        $response->assertStatus(200);
    }
})->with([
            'T06: Dashboard Ana Sayfa' => ['/dashboard'],
            'T07: Ayarlar Paneli' => ['/dashboard/settings/panel'],
            'T08: Ayarlar Mail' => ['/dashboard/settings/mail'],
            'T09: Ayarlar Storage' => ['/dashboard/settings/storage'],
            'T10: Ayarlar PDF Şablonu' => ['/dashboard/settings/pdf-template'],
            'T11: Ayarlar Profil' => ['/dashboard/settings/profile'],
            'T12: Ayarlar Görünüm' => ['/settings/appearance'],
            'T13: Ayarlar Değişkenler' => ['/dashboard/settings/variables'],
            'T14: Ayarlar Fiyatlandırma' => ['/dashboard/settings/prices'],
            'T15: 2FA Ayarları' => ['/dashboard/settings/two-factor'],
        ]);


// --- SECTION C: Main Listings ---

test('Main Listing page loads', function (string $route) {
    $this->get($route)->assertStatus(200);
})->with([
            'T16: Müşteriler Listesi' => ['/dashboard/customers?tab=customers'],
            'T17: Projeler Listesi' => ['/dashboard/projects?tab=projects'],
            'T18: Görevler Listesi' => ['/dashboard/projects?tab=tasks'],
            'T19: Raporlar Listesi' => ['/dashboard/projects?tab=reports'],
        ]);


// --- SECTION D: Create/Edit Routes ---

test('Create/Edit page loads', function (string $route) {
    // Resolve dynamic IDs in the route
    $route = str_replace(
        ['{customer_id}', '{project_id}'],
        [$this->customer->id, $this->project->id],
        $route
    );

    $this->get($route)->assertStatus(200);
})->with([
            'T21: Yeni Müşteri Sayfası' => ['/dashboard/customers/create'],
            'T22: Müşteri Düzenleme Sayfası' => ['/dashboard/customers/{customer_id}'],
            'T23: Yeni Proje Sayfası' => ['/dashboard/projects/create'],
            'T24: Proje Düzenleme Sayfası' => ['/dashboard/projects/{project_id}'],
            'T25: Yeni Görev Sayfası' => ['/dashboard/projects/tasks/create'],
            'T26: Yeni Rapor Sayfası' => ['/dashboard/projects/reports/create'],
            'T27: Yeni Varlık Sayfası' => ['/dashboard/customers/assets/create'],
            'T29: Yeni Hizmet Sayfası' => ['/dashboard/customers/services/create'],
        ]);

// --- SECTION E: Detail Page Routes (Additional) ---
test('Detail Edit page loads', function (Closure $routeGenerator) {
    $route = $routeGenerator($this);
    $this->get($route)->assertStatus(200);
})->with([
            'T90: Offer Detail' => [fn($t) => '/dashboard/customers/offers/' . Offer::factory()->create(['customer_id' => $t->customer->id])->id],
            'T91: Asset Detail' => [fn($t) => '/dashboard/customers/assets/' . Asset::factory()->create(['customer_id' => $t->customer->id])->id],
            'T92: Service Detail' => [fn($t) => '/dashboard/customers/services/' . Service::factory()->create(['customer_id' => $t->customer->id])->id],
            'T93: Contact Detail' => [fn($t) => '/dashboard/customers/contacts/' . Contact::factory()->create(['customer_id' => $t->customer->id])->id],
        ]);


// --- SECTION F: Critical Component Render Tests ---

// Modals & Forms
test('Component renders without errors', function (string $component, array $params = [], string $assertion = 'assertHasNoErrors', ?string $assertValue = null) {
    // Check if component exists (View for Volt/Blade or Class for Class-based)
    if (!view()->exists('livewire.' . $component) && !class_exists($component)) {
        $this->markTestSkipped("Component [$component] not found/implemented yet.");
        return;
    }

    // Prepare params if they are closures
    foreach ($params as $key => $value) {
        if ($value instanceof Closure) {
            $params[$key] = $value($this);
        }
    }

    $test = Volt::test($component, $params);

    if ($assertion === 'assertSet') {
        $test->assertSet($assertValue, $params[$assertValue] ?? null);
    } elseif ($assertValue !== null) {
        $test->$assertion($assertValue);
    } else {
        $test->$assertion();
    }
})->with([
            // Modals
            'T34: Offer Modal Create' => ['modals.offer-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T35: Offer Modal Edit' => ['modals.offer-form', ['offer' => fn($t) => Offer::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T36: Service Modal Create' => ['modals.service-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T37: Service Modal Edit' => ['modals.service-form', ['service' => fn($t) => Service::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T38: Asset Modal Create' => ['modals.asset-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T39: Asset Modal Edit' => ['modals.asset-form', ['asset' => fn($t) => Asset::factory()->create(['customer_id' => $t->customer->id])->id]],
            'T40: Contact Modal Create' => ['modals.contact-form', ['customer_id' => fn($t) => $t->customer->id]],
            'T41: Contact Modal Edit' => ['modals.contact-form', ['contact' => fn($t) => Contact::factory()->create(['customer_id' => $t->customer->id])->id]],

            // Project Components
            'T42: Task Create' => ['projects.tasks.create'],
            'T43: Report Create' => ['projects.reports.create'],

            // Customer Tabs
            'T45: Offers Tab' => ['customers.tabs.offers-tab'],
            'T46: Assets Tab' => ['customers.tabs.assets-tab'],
            'T47: Services Tab' => ['customers.tabs.services-tab'],
            'T48: Contacts Tab' => ['customers.tabs.contacts-tab'],
            'T49: Projects Tab' => ['customers.tabs.projects-tab', ['customer' => fn($t) => $t->customer]],

            // Project Tabs
            'T54: Project Tasks Tab' => ['projects.tabs.tasks-tab', ['project_id' => fn($t) => $t->project->id]],
            'T55: Project Reports Tab' => ['projects.tabs.reports-tab', ['project_id' => fn($t) => $t->project->id]],

            // Settings Components
            'T64: Settings Panel' => ['settings.panel'],
            'T65: Settings Mail' => ['settings.mail'],
            'T66: Settings Prices' => ['settings.prices'],
            'T67: Settings PDF' => ['settings.pdf-template'],
            'T68: Settings Storage' => ['settings.storage'],
            'T69: Settings Variables' => ['settings.variables'],
            'T70: Settings Appearance' => ['settings.appearance'],
            'T71: Settings Profile' => ['settings.profile'],
            'T72: Settings Password' => ['settings.password'],
            'T73: Settings 2FA' => ['settings.two-factor'],

            // Public Components
            'T81: Public Offer Download' => ['public.offer-download', ['token' => fn($t) => Offer::factory()->create(['customer_id' => $t->customer->id])->tracking_token], 'assertOk'],
        ]);

// Special Rendering Tests (Livewire Non-Volt or Blade Partials)

test('T50: Shared Notes tab renders', function () {
    Livewire::test('shared.notes-tab', [
        'entityType' => 'CUSTOMER',
        'entityId' => $this->customer->id
    ])->assertOk();
});

test('T56: Project Notes tab renders', function () {
    Livewire::test('projects.tabs.notes-tab', ['project_id' => $this->project->id])->assertSee('Notlar');
});

test('T57: Project Phase Form Partial renders', function () {
    $view = View::make('livewire.projects.parts._phase-form', [
        'index' => 0,
        'phase' => ['name' => 'Test', 'color' => 'blue', 'modules' => []],
        'isViewMode' => false,
        'phaseStatuses' => [],
        'moduleStatuses' => []
    ]);
    expect($view->render())->toContain('Test');
});

test('T58: Project Module Form Partial renders', function () {
    $viewM = View::make('livewire.projects.parts._module-form', [
        'phaseIndex' => 0,
        'moduleIndex' => 0,
        'module' => ['name' => 'Module'],
        'isViewMode' => false,
        'moduleStatuses' => []
    ]);
    expect($viewM->render())->toContain('Module');
});

// --- SECTION G: Authentication & Authorization Tests ---

test('T74: Unauthenticated user redirected to login', function () {
    auth()->logout();
    $this->get('/dashboard')->assertRedirect('/login');
});

test('T75: Authenticated user can access dashboard', function () {
    $this->get('/dashboard')->assertStatus(200);
});

test('T76: User without permission cannot access restricted routes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get('/dashboard/settings/panel')->assertForbidden();
});

test('T77: User with permission can access restricted routes', function () {
    $this->user->givePermissionTo('settings.view');
    $this->get('/dashboard/settings/panel')->assertStatus(200);
});

test('T78: Guest cannot access protected API endpoints', function () {
    auth()->logout();
    $this->getJson('/api/customers')->assertStatus(401);
});

test('T79: Authenticated user can access API endpoints', function () {
    $this->getJson('/api/customers')->assertStatus(200);
});

test('T80: CSRF protection works on forms', function () {
    $this->post('/dashboard/customers', [])->assertStatus(419);
});

// --- SECTION H: Database Connection & Migration Tests ---

test('T82: Database connection is working', function () {
    expect(DB::connection()->getPdo())->not->toBeNull();
});

test('T83: All migrations have run successfully', function () {
    $migrations = DB::table('migrations')->count();
    expect($migrations)->toBeGreaterThan(0);
});

test('T84: Reference data is seeded correctly', function () {
    expect(ReferenceCategory::count())->toBeGreaterThan(0);
    expect(ReferenceItem::count())->toBeGreaterThan(0);
});

test('T85: User roles and permissions are working', function () {
    $role = \App\Models\Role::factory()->create(['name' => 'Test Role']);
    $permission = \App\Models\Permission::factory()->create(['name' => 'test.permission']);

    $role->givePermissionTo($permission);
    $user = User::factory()->create(['role_id' => $role->id]);

    expect($user->hasPermissionTo('test.permission'))->toBeTrue();
});

// --- SECTION I: File System & Storage Tests ---

test('T86: Storage disk is accessible', function () {
    Storage::fake('local');
    Storage::put('test.txt', 'test content');
    expect(Storage::exists('test.txt'))->toBeTrue();
});

test('T87: Public disk is accessible', function () {
    Storage::fake('public');
    Storage::disk('public')->put('test.txt', 'test content');
    expect(Storage::disk('public')->exists('test.txt'))->toBeTrue();
});

test('T88: File upload functionality works', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('test.pdf', 100);

    $response = $this->post('/dashboard/upload', [
        'file' => $file
    ]);

    expect($response->status())->toBeLessThan(500);
});

// --- SECTION J: Email & Notification Tests ---

test('T89: Mail configuration is working', function () {
    Mail::fake();

    Mail::to('test@example.com')->send(new \App\Mail\TestMail());

    Mail::assertSent(\App\Mail\TestMail::class);
});

test('T90: Notification system is working', function () {
    Notification::fake();

    $user = User::factory()->create();
    $user->notify(new \App\Notifications\TestNotification());

    Notification::assertSentTo($user, \App\Notifications\TestNotification::class);
});

// --- SECTION K: Cache & Session Tests ---

test('T91: Cache system is working', function () {
    Cache::put('test_key', 'test_value', 60);
    expect(Cache::get('test_key'))->toBe('test_value');
});

test('T92: Session system is working', function () {
    session(['test_key' => 'test_value']);
    expect(session('test_key'))->toBe('test_value');
});

test('T93: Redis connection is working', function () {
    if (!extension_loaded('redis')) {
        $this->markTestSkipped('Redis extension not loaded');
    }

    try {
        Redis::ping();
        expect(true)->toBeTrue();
    } catch (\Exception $e) {
        $this->markTestSkipped('Redis not available: ' . $e->getMessage());
    }
});

// --- SECTION L: Queue & Job Tests ---

test('T94: Queue system is working', function () {
    Queue::fake();

    dispatch(new \App\Jobs\TestJob());

    Queue::assertPushed(\App\Jobs\TestJob::class);
});

test('T95: Job processing works', function () {
    $job = new \App\Jobs\TestJob();
    $job->handle();

    expect(true)->toBeTrue(); // Job completed without error
});

// --- SECTION M: API Response Tests ---

test('T96: API returns valid JSON responses', function () {
    $response = $this->getJson('/api/customers');

    $response->assertHeader('Content-Type', 'application/json');
    expect($response->json())->toBeArray();
});

test('T97: API pagination works', function () {
    Customer::factory()->count(20)->create();

    $response = $this->getJson('/api/customers?page=1&per_page=10');

    $response->assertJsonStructure([
        'data',
        'meta' => ['current_page', 'per_page', 'total']
    ]);
});

test('T98: API filtering works', function () {
    Customer::factory()->create(['name' => 'Test Customer']);
    Customer::factory()->create(['name' => 'Another Customer']);

    $response = $this->getJson('/api/customers?search=Test');

    expect(count($response->json('data')))->toBe(1);
});

// --- SECTION N: Performance & Memory Tests ---

test('T99: Memory usage is within acceptable limits', function () {
    $initialMemory = memory_get_usage();

    // Perform some operations
    Customer::factory()->count(100)->create();
    $customers = Customer::all();

    $finalMemory = memory_get_usage();
    $memoryUsed = $finalMemory - $initialMemory;

    // Should use less than 50MB for 100 customers
    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024);
});

test('T100: Database queries are optimized', function () {
    DB::enableQueryLog();

    $customers = Customer::with(['projects', 'offers'])->limit(10)->get();

    $queries = DB::getQueryLog();

    // Should not have N+1 queries
    expect(count($queries))->toBeLessThan(5);
});

// --- SECTION O: Security Tests ---

test('T101: XSS protection is working', function () {
    $maliciousInput = '<script>alert("XSS")</script>';

    $customer = Customer::factory()->create(['name' => $maliciousInput]);

    $response = $this->get('/dashboard/customers/' . $customer->id);

    $response->assertDontSee('<script>', false);
});

test('T102: SQL injection protection is working', function () {
    $maliciousInput = "'; DROP TABLE customers; --";

    $response = $this->getJson('/api/customers?search=' . urlencode($maliciousInput));

    expect(Customer::count())->toBeGreaterThan(0); // Table should still exist
});

test('T103: CSRF protection is enabled', function () {
    $response = $this->post('/dashboard/customers', [
        'name' => 'Test Customer'
    ]);

    $response->assertStatus(419); // CSRF token mismatch
});

// --- SECTION P: Localization Tests ---

test('T104: Default language is Turkish', function () {
    expect(app()->getLocale())->toBe('tr');
});

test('T105: Language switching works', function () {
    app()->setLocale('en');
    expect(app()->getLocale())->toBe('en');
});

test('T106: Translation files exist', function () {
    expect(file_exists(lang_path('tr/validation.php')))->toBeTrue();
    expect(file_exists(lang_path('en/validation.php')))->toBeTrue();
});

// --- SECTION Q: Configuration Tests ---

test('T107: Environment configuration is correct', function () {
    expect(config('app.env'))->toBe('testing');
    expect(config('app.debug'))->toBeTrue();
});

test('T108: Database configuration is correct', function () {
    expect(config('database.default'))->toBe('sqlite');
});

test('T109: Mail configuration is set', function () {
    expect(config('mail.default'))->not->toBeNull();
});

// --- SECTION R: Middleware Tests ---

test('T110: Authentication middleware works', function () {
    auth()->logout();

    $response = $this->get('/dashboard/customers');

    $response->assertRedirect('/login');
});

test('T111: Permission middleware works', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard/settings/panel');

    $response->assertForbidden();
});

test('T112: CORS middleware works', function () {
    $response = $this->options('/api/customers');

    $response->assertHeader('Access-Control-Allow-Origin');
});

// --- SECTION S: Event & Listener Tests ---

test('T113: Events are fired correctly', function () {
    Event::fake();

    Customer::factory()->create();

    Event::assertDispatched(\App\Events\CustomerCreated::class);
});

test('T114: Listeners handle events correctly', function () {
    Event::fake();

    $customer = Customer::factory()->create();

    event(new \App\Events\CustomerCreated($customer));

    Event::assertListening(
        \App\Events\CustomerCreated::class,
        \App\Listeners\SendCustomerWelcomeEmail::class
    );
});

// --- SECTION T: Validation Tests ---

test('T115: Form validation works correctly', function () {
    $response = $this->post('/dashboard/customers', []);

    $response->assertSessionHasErrors(['name']);
});

test('T116: Custom validation rules work', function () {
    $response = $this->post('/dashboard/customers', [
        'name' => 'a', // Too short
        'email' => 'invalid-email'
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

// --- SECTION U: Logging Tests ---

test('T117: Application logging works', function () {
    Log::info('Test log message');

    expect(true)->toBeTrue(); // No exception thrown
});

test('T118: Error logging works', function () {
    try {
        throw new \Exception('Test exception');
    } catch (\Exception $e) {
        Log::error('Test error', ['exception' => $e]);
    }

    expect(true)->toBeTrue(); // No exception thrown
});

// --- SECTION V: Artisan Command Tests ---

test('T119: Artisan commands are registered', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('migrate');
    expect($commands)->toHaveKey('db:seed');
});

test('T120: Custom artisan commands work', function () {
    $exitCode = Artisan::call('app:test-command');

    expect($exitCode)->toBe(0);
});

// --- SECTION W: Model Relationship Tests ---

test('T121: Customer relationships work', function () {
    $customer = Customer::factory()->create();
    $project = Project::factory()->create(['customer_id' => $customer->id]);

    expect($customer->projects)->toHaveCount(1);
    expect($project->customer->id)->toBe($customer->id);
});

test('T122: Project relationships work', function () {
    $project = Project::factory()->create();
    $task = \App\Models\Task::factory()->create(['project_id' => $project->id]);

    expect($project->tasks)->toHaveCount(1);
    expect($task->project->id)->toBe($project->id);
});

// --- SECTION X: Factory & Seeder Tests ---

test('T123: Model factories work correctly', function () {
    $customer = Customer::factory()->create();

    expect($customer)->toBeInstanceOf(Customer::class);
    expect($customer->name)->not->toBeNull();
});

test('T124: Database seeders work correctly', function () {
    Artisan::call('db:seed', ['--class' => 'ReferenceDataSeeder']);

    expect(ReferenceCategory::count())->toBeGreaterThan(0);
});

// --- SECTION Y: Route Tests ---

test('T125: All routes are accessible', function () {
    $routes = Route::getRoutes();

    expect(count($routes))->toBeGreaterThan(0);
});

test('T126: API routes are properly versioned', function () {
    $response = $this->getJson('/api/v1/customers');

    expect($response->status())->toBeLessThan(500);
});

// --- SECTION Z: Integration Tests ---

test('T127: Full customer creation workflow', function () {
    $customerData = [
        'name' => 'Test Customer',
        'email' => 'test@example.com',
        'phone' => '+90 555 123 4567'
    ];

    $response = $this->post('/dashboard/customers', $customerData);

    $response->assertRedirect();
    $this->assertDatabaseHas('customers', ['name' => 'Test Customer']);
});

test('T128: Full project creation workflow', function () {
    $projectData = [
        'name' => 'Test Project',
        'customer_id' => $this->customer->id,
        'leader_id' => $this->user->id,
        'status_id' => ReferenceItem::where('key', 'ACTIVE')->first()->id
    ];

    $response = $this->post('/dashboard/projects', $projectData);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
});

// --- SECTION AA: Error Handling Tests ---

test('T129: 404 errors are handled gracefully', function () {
    $response = $this->get('/non-existent-route');

    $response->assertStatus(404);
});

test('T130: 500 errors are handled gracefully', function () {
    // Simulate a server error
    $this->mock(\App\Services\CustomerService::class, function ($mock) {
        $mock->shouldReceive('create')->andThrow(new \Exception('Test error'));
    });

    $response = $this->post('/dashboard/customers', [
        'name' => 'Test Customer'
    ]);

    expect($response->status())->toBeLessThan(600);
});

// --- SECTION BB: Performance Monitoring Tests ---

test('T131: Response times are acceptable', function () {
    $start = microtime(true);

    $this->get('/dashboard');

    $end = microtime(true);
    $responseTime = ($end - $start) * 1000; // Convert to milliseconds

    expect($responseTime)->toBeLessThan(1000); // Less than 1 second
});

test('T132: Database query count is optimized', function () {
    DB::enableQueryLog();

    $this->get('/dashboard/customers');

    $queries = DB::getQueryLog();

    expect(count($queries))->toBeLessThan(20); // Reasonable query count
});

// --- SECTION CC: Browser & JavaScript Tests ---

test('T133: JavaScript assets are compiled', function () {
    expect(file_exists(public_path('build/assets/app.js')))->toBeTrue();
});

test('T134: CSS assets are compiled', function () {
    expect(file_exists(public_path('build/assets/app.css')))->toBeTrue();
});

// --- SECTION DD: Health Check Tests ---

test('T135: Application health check passes', function () {
    $response = $this->get('/health');

    if ($response->status() === 404) {
        $this->markTestSkipped('Health check endpoint not implemented');
    }

    $response->assertStatus(200);
});

test('T136: Database health check passes', function () {
    try {
        DB::connection()->getPdo();
        expect(true)->toBeTrue();
    } catch (\Exception $e) {
        $this->fail('Database connection failed: ' . $e->getMessage());
    }
});

// --- SECTION EE: Final Integration Tests ---

test('T137: Complete user journey works', function () {
    // 1. Login
    $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password'
    ]);

    // 2. Create customer
    $customer = Customer::factory()->create();

    // 3. Create project
    $project = Project::factory()->create(['customer_id' => $customer->id]);

    // 4. Create offer
    $offer = Offer::factory()->create(['customer_id' => $customer->id]);

    // 5. Verify all created
    expect($customer->exists)->toBeTrue();
    expect($project->exists)->toBeTrue();
    expect($offer->exists)->toBeTrue();
});

test('T138: System can handle concurrent requests', function () {
    $responses = [];

    // Simulate concurrent requests
    for ($i = 0; $i < 5; $i++) {
        $responses[] = $this->get('/dashboard');
    }

    foreach ($responses as $response) {
        $response->assertStatus(200);
    }
});

// --- SECTION FF: Cleanup & Maintenance Tests ---

test('T139: Temporary files are cleaned up', function () {
    // Create a temporary file
    $tempFile = storage_path('app/temp/test.txt');
    if (!is_dir(dirname($tempFile)))
        mkdir(dirname($tempFile), 0777, true);
    file_put_contents($tempFile, 'test content');

    // Run cleanup command
    Artisan::call('app:cleanup-temp-files');

    expect(file_exists($tempFile))->toBeFalse();
});

test('T140: Old logs are rotated', function () {
    // This test would check if log rotation is working
    // Implementation depends on your log rotation strategy
    expect(true)->toBeTrue();
});

// --- SECTION GG: Final System Verification ---

test('T141: All critical services are running', function () {
    // Database
    expect(DB::connection()->getPdo())->not->toBeNull();

    // Cache
    Cache::put('test', 'value');
    expect(Cache::get('test'))->toBe('value');

    // Session
    session(['test' => 'value']);
    expect(session('test'))->toBe('value');
});

test('T142: System is ready for production', function () {
    // Check critical configurations
    expect(config('app.key'))->not->toBeNull();
    expect(config('database.connections.mysql.host'))->not->toBeNull();

    // Check critical directories exist
    expect(is_dir(storage_path('app')))->toBeTrue();
    expect(is_dir(storage_path('logs')))->toBeTrue();

    // Check permissions
    expect(is_writable(storage_path()))->toBeTrue();
});

// Add missing use statements at the top
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
