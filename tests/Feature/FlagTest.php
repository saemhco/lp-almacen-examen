<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlagTest extends TestCase
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

    public function test_admin_flag_is_the_calculated_permission_count(): void
    {
        $total = Role::query()->where('name', 'admin')->firstOrFail()->permissions()->count();

        $this->get('/flags/admin')
            ->assertOk()
            ->assertSee('Este flag se calcula')
            ->assertSee('FLAG{'.$total.'}', false);
    }

    public function test_mensaje_requires_an_almacenista_token_and_returns_respuesta(): void
    {
        $this->getJson('/api/mensaje')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Usa tu token para poder ver el mensaje.');

        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
        ]);

        $adminLogin = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertOk();

        $this->withToken($adminLogin->json('data.token'))
            ->getJson('/api/mensaje')
            ->assertForbidden()
            ->assertJsonPath('message', 'Usa el token de un usuario almacenista para poder ver el mensaje.');

        $staffLogin = $this->postJson('/api/login', [
            'email' => 'almacenista@example.com',
            'password' => 'Almacen2026',
        ])->assertOk();

        $this->withToken($staffLogin->json('data.token'))
            ->getJson('/api/mensaje')
            ->assertOk()
            ->assertJsonPath('message', 'El flag es data.respuesta.')
            ->assertJsonPath('data.respuesta', 'FLAG{token-almacenista}');
    }

    public function test_supervisor_flag_is_the_calculated_permission_diff(): void
    {
        $supervisor = Role::query()->where('name', 'supervisor')->firstOrFail();
        $staff = Role::query()->where('name', 'almacenista')->firstOrFail();

        $flag = $supervisor->permissions()->pluck('name')
            ->diff($staff->permissions()->pluck('name'))
            ->sort()
            ->values()
            ->implode(',');

        $this->get('/flags/supervisor')
            ->assertOk()
            ->assertSee('Este flag se calcula')
            ->assertSee($flag, false);
    }
}
