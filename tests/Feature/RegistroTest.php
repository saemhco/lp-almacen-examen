<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_guest_cannot_register(): void
    {
        $this->postJson('/api/registro', $this->payload())
            ->assertUnauthorized();
    }

    public function test_almacenista_cannot_register(): void
    {
        $staff = User::query()->where('email', 'almacenista@example.com')->firstOrFail();

        $this->actingAs($staff, 'sanctum')
            ->postJson('/api/registro', $this->payload())
            ->assertForbidden()
            ->assertJsonPath('message', 'Usa el token de un usuario administrador.');
    }

    public function test_admin_gets_a_hint_while_the_role_name_has_an_extra_letter(): void
    {
        Http::fake();

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/registro', $this->payload())
            ->assertForbidden()
            ->assertJsonPath('message', 'El nombre del rol tiene una letra de más. Abre config/udh.php y corrige el nombre del rol.');

        Http::assertNothingSent();
    }

    public function test_admin_with_the_permission_sends_team_metadata_and_returns_the_code(): void
    {
        Config::set('udh.rol', 'admin');

        Http::fake([
            'https://panki.quipu.club/api/v1/udh/lp1' => Http::response([
                'codigo' => 'UDH-LP1-042',
            ], 200),
        ]);

        $permission = Permission::query()->firstOrCreate(
            ['name' => 'registro.create'],
            ['description' => 'Crear registro'],
        );

        $adminRole = Role::query()->where('name', 'admin')->firstOrFail();
        $adminRole->permissions()->syncWithoutDetaching([$permission->id]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role_id' => $adminRole->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/registro', $this->payload())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'La respuesta está en data.mensaje.')
            ->assertJsonPath('data.mensaje', 'UDH-LP1-042');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://panki.quipu.club/api/v1/udh/lp1'
                && $request['nombres'] === 'Saúl'
                && $request['apellidos'] === 'Escandón'
                && $request['codigo_universitario'] === '20201234'
                && $request['metadatos']['facultad'] === 'Ingeniería'
                && $request['metadatos']['programa_academico'] === 'Sistemas e Informática'
                && $request['metadatos']['ciclo'] === 8
                && $request['metadatos']['ip'] === '127.0.0.1'
                && array_key_exists('agente', $request['metadatos'])
                && ! $request->hasHeader('Authorization');
        });
    }

    private function payload(): array
    {
        return [
            'nombres' => 'Saúl',
            'apellidos' => 'Escandón',
            'codigo_universitario' => '20201234',
        ];
    }
}
