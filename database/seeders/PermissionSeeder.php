<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'items' => 'items',
            'categories' => 'categorias',
            'suppliers' => 'proveedores',
            'locations' => 'ubicaciones',
            'movements' => 'movimientos',
            'users' => 'usuarios',
            'roles' => 'roles',
            'permissions' => 'permisos',
        ];

        $actions = [
            'view' => 'ver',
            'create' => 'crear',
            'update' => 'actualizar',
            'delete' => 'eliminar',
        ];

        foreach ($resources as $resource => $label) {
            foreach ($actions as $action => $actionLabel) {
                Permission::query()->firstOrCreate(
                    ['name' => $resource.'.'.$action],
                    ['description' => ucfirst($actionLabel).' '.$label],
                );
            }
        }

        Permission::query()->firstOrCreate(
            ['name' => 'registro.crate'],
            ['description' => 'Crear registro'],
        );
    }
}
