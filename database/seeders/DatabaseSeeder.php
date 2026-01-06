<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $role = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator']
        );

        User::firstOrCreate(
            ['email' => 'admin@mediaclick.com.tr'],
            [
                'name' => 'Admin',
                'password' => 'admin', // Will be hashed by model cast
                'email_verified_at' => now(),
                'role_id' => $role->id,
            ]
        );
    }
}
