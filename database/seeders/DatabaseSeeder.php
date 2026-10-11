<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        User::create([
            'name' => 'Admin 02',
            'email' => 'admin@example.com',
            'password' => bcrypt('Admim2026'),
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
        ]);

        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            LocationSeeder::class,
            ItemSeeder::class,
            MovementSeeder::class,
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
