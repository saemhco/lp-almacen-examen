<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
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

    public function test_validation_errors_keep_the_api_format(): void
    {
        $this->postJson('/api/login', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation Error')
            ->assertJsonStructure(['data' => ['email', 'password']]);

        $admin = $this->userWithRole('admin', 'admin@example.com');
        $user = User::factory()->create([
            'email' => 'mismo@example.com',
            'role_id' => Role::query()->where('name', 'almacenista')->value('id'),
        ]);

        $this->actingAs($admin, 'sanctum')
            ->putJson('/api/users/'.$user->id, [
                'name' => 'Mismo',
                'email' => 'mismo@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('data.email', 'mismo@example.com');
    }

    public function test_guest_cannot_list_users(): void
    {
        $this->getJson('/api/users')
            ->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthenticated');
    }

    public function test_admin_can_manage_users_roles_and_permissions(): void
    {
        $admin = $this->userWithRole('admin', 'admin@example.com');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('success', true);

        $staffRole = Role::query()->where('name', 'almacenista')->firstOrFail();

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Nuevo',
                'email' => 'nuevo@example.com',
                'password' => 'password',
                'role_id' => $staffRole->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'nuevo@example.com');

        $viewItems = Permission::query()->where('name', 'items.view')->firstOrFail();

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'consulta',
                'description' => 'Solo consulta items',
                'permission_ids' => [$viewItems->id],
            ])
            ->assertCreated()
            ->assertJsonPath('data.permissions.0.name', 'items.view');

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'name' => 'reports.view',
                'description' => 'Ver reportes',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'reports.view');
    }

    public function test_almacenista_cannot_manage_users_and_can_view_items(): void
    {
        $staff = User::query()->where('email', 'almacenista@example.com')->firstOrFail();

        $this->actingAs($staff, 'sanctum')
            ->getJson('/api/users')
            ->assertForbidden()
            ->assertJsonPath('message', 'Forbidden');

        $this->actingAs($staff, 'sanctum')
            ->postJson('/api/items', [])
            ->assertForbidden();

        $this->actingAs($staff, 'sanctum')
            ->getJson('/api/items')
            ->assertOk();
    }

    public function test_login_returns_a_token_and_logout_revokes_it(): void
    {
        $this->userWithRole('admin', 'admin@example.com');

        $login = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertOk();

        $token = $login->json('data.token');

        $this->withToken($token)
            ->getJson('/api/roles')
            ->assertOk();

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame(0, $admin->tokens()->count());

        $this->app['auth']->forgetGuards();

        $this->withToken($token)
            ->getJson('/api/roles')
            ->assertUnauthorized();
    }

    private function userWithRole(string $role, string $email): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => 'password',
            'role_id' => Role::query()->where('name', $role)->value('id'),
        ]);
    }
}
