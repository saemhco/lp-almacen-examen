<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::query()->firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administra el catalogo y los movimientos del almacen'],
        );

        $staff = Role::query()->firstOrCreate(
            ['name' => 'almacenista'],
            ['description' => 'Consulta el almacen y registra entradas y salidas'],
        );

        $supervisor = Role::query()->firstOrCreate(
            ['name' => 'supervisor'],
            ['description' => 'Revisa el almacen, corrige items y ajusta movimientos'],
        );

        $admin->permissions()->sync(Permission::query()->pluck('id'));

        $warehouseViews = [
            'items.view',
            'categories.view',
            'suppliers.view',
            'locations.view',
            'movements.view',
        ];

        $staffPermissions = Permission::query()
            ->whereIn('name', [...$warehouseViews, 'movements.create'])
            ->pluck('id');

        $staff->permissions()->sync($staffPermissions);

        $supervisorPermissions = Permission::query()
            ->whereIn('name', [
                ...$warehouseViews,
                'items.update',
                'movements.create',
                'movements.update',
            ])
            ->pluck('id');

        $supervisor->permissions()->sync($supervisorPermissions);

        User::query()->where('email', 'admin@example.com')->update([
            'role_id' => $admin->id,
        ]);

        User::query()->firstOrCreate(
            ['email' => 'almacenista@example.com'],
            [
                'name' => 'Almacenista',
                'password' => 'Almacen2026',
                'role_id' => $staff->id,
            ],
        );

        User::query()->firstOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'name' => 'Supervisor',
                'password' => 'Supervisor2026',
                'role_id' => $supervisor->id,
            ],
        );
    }
}
