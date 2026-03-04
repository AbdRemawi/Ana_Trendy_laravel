<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Authorization Test Suite.
 *
 * Tests middleware protection, policy enforcement, and access control
 * for role and permission management endpoints.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminWithPermission;
    protected User $adminWithoutPermission;
    protected User $regularUser;

    /**
     * Set up test fixtures with different user types.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('spatie.permission.cache');

        // Create permissions
        $manageRoles = Permission::firstOrCreate(['name' => 'manage roles']);
        $managePermissions = Permission::firstOrCreate(['name' => 'manage permissions']);

        // Create super_admin role with all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo([$manageRoles, $managePermissions]);

        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($manageRoles);

        // Create super admin user
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        // Create admin with manage roles permission
        $this->adminWithPermission = User::factory()->create();
        $this->adminWithPermission->assignRole('admin');

        // Create admin without any special permissions
        $regularAdminRole = Role::firstOrCreate(['name' => 'regular_admin']);
        $this->adminWithoutPermission = User::factory()->create();
        $this->adminWithoutPermission->assignRole('regular_admin');

        // Create regular user
        $this->regularUser = User::factory()->create();
    }

    /**
     * ROLE ENDPOINT AUTHORIZATION TESTS
     */

    /** @test */
    public function guest_cannot_access_role_endpoints(): void
    {
        $role = Role::create(['name' => 'test_role']);

        $endpoints = [
            ['method' => 'get', 'url' => route('admin.roles.index')],
            ['method' => 'get', 'url' => route('admin.roles.create')],
            ['method' => 'post', 'url' => route('admin.roles.store')],
            ['method' => 'get', 'url' => route('admin.roles.show', $role)],
            ['method' => 'get', 'url' => route('admin.roles.edit', $role)],
            ['method' => 'put', 'url' => route('admin.roles.update', $role)],
            ['method' => 'delete', 'url' => route('admin.roles.destroy', $role)],
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->{$endpoint['method']}($endpoint['url']);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function user_without_permission_cannot_create_role(): void
    {
        $response = $this->actingAs($this->adminWithoutPermission)
            ->post(route('admin.roles.store'), ['name' => 'test_role']);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_without_permission_cannot_view_roles(): void
    {
        $response = $this->actingAs($this->adminWithoutPermission)
            ->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_with_permission_can_access_role_endpoints(): void
    {
        $role = Role::create(['name' => 'test_role']);

        // User with manage roles permission can access
        $response = $this->actingAs($this->adminWithPermission)
            ->get(route('admin.roles.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminWithPermission)
            ->get(route('admin.roles.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminWithPermission)
            ->get(route('admin.roles.show', $role));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminWithPermission)
            ->get(route('admin.roles.edit', $role));
        $response->assertStatus(200);
    }

    /**
     * PERMISSION ENDPOINT AUTHORIZATION TESTS
     */

    /** @test */
    public function guest_cannot_access_permission_endpoints(): void
    {
        $permission = Permission::create(['name' => 'test_permission']);

        $endpoints = [
            ['method' => 'get', 'url' => route('admin.permissions.index')],
            ['method' => 'get', 'url' => route('admin.permissions.create')],
            ['method' => 'post', 'url' => route('admin.permissions.store')],
            ['method' => 'get', 'url' => route('admin.permissions.show', $permission)],
            ['method' => 'get', 'url' => route('admin.permissions.edit', $permission)],
            ['method' => 'put', 'url' => route('admin.permissions.update', $permission)],
            ['method' => 'delete', 'url' => route('admin.permissions.destroy', $permission)],
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->{$endpoint['method']}($endpoint['url']);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function user_without_manage_permissions_cannot_access_permission_endpoints(): void
    {
        $permission = Permission::create(['name' => 'test permission']);

        $endpoints = [
            ['method' => 'get', 'url' => route('admin.permissions.index')],
            ['method' => 'get', 'url' => route('admin.permissions.create')],
            ['method' => 'post', 'url' => route('admin.permissions.store'), 'data' => ['name' => 'new permission']],
            ['method' => 'get', 'url' => route('admin.permissions.show', $permission)],
            ['method' => 'get', 'url' => route('admin.permissions.edit', $permission)],
            ['method' => 'put', 'url' => route('admin.permissions.update', $permission), 'data' => ['name' => 'updated']],
            ['method' => 'delete', 'url' => route('admin.permissions.destroy', $permission)],
        ];

        foreach ($endpoints as $endpoint) {
            $request = $this->actingAs($this->adminWithPermission)
                ->{$endpoint['method']}($endpoint['url'], $endpoint['data'] ?? []);

            $request->assertStatus(403);
        }
    }

    /** @test */
    public function user_with_manage_permissions_can_access_permission_endpoints(): void
    {
        $permission = Permission::create(['name' => 'test permission']);

        $endpoints = [
            ['method' => 'get', 'url' => route('admin.permissions.index')],
            ['method' => 'get', 'url' => route('admin.permissions.create')],
            ['method' => 'get', 'url' => route('admin.permissions.show', $permission)],
            ['method' => 'get', 'url' => route('admin.permissions.edit', $permission)],
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($this->superAdmin)
                ->{$endpoint['method']}($endpoint['url']);

            $response->assertStatus(200);
        }
    }

    /**
     * REGULAR USER RESTRICTION TESTS
     */

    /** @test */
    public function regular_user_cannot_manage_roles(): void
    {
        $role = Role::create(['name' => 'test_role']);

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.roles.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.roles.store'), ['name' => 'new_role']);
        $response->assertStatus(403);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.roles.destroy', $role));
        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_manage_permissions(): void
    {
        $permission = Permission::create(['name' => 'test permission']);

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.permissions.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.permissions.store'), ['name' => 'new permission']);
        $response->assertStatus(403);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.permissions.destroy', $permission));
        $response->assertStatus(403);
    }

    /**
     * SUPER ADMIN PRIVILEGE TESTS
     */

    /** @test */
    public function super_admin_has_full_access_to_roles(): void
    {
        $role = Role::create(['name' => 'test_role']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), ['name' => 'new_role']);
        $response->assertRedirect();

        $this->assertDatabaseHas('roles', ['name' => 'new_role']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.roles.update', $role), ['name' => 'updated_role']);
        $response->assertRedirect();
    }

    /** @test */
    public function super_admin_has_full_access_to_permissions(): void
    {
        $permission = Permission::create(['name' => 'test permission']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.permissions.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), ['name' => 'new permission']);
        $response->assertRedirect();

        $this->assertDatabaseHas('permissions', ['name' => 'new_permission']);
    }

    /**
     * ROUTE PROTECTION TESTS
     */

    /** @test */
    public function all_admin_role_routes_are_protected(): void
    {
        $role = Role::create(['name' => 'test']);

        $routes = [
            route('admin.roles.index'),
            route('admin.roles.create'),
            route('admin.roles.show', $role),
            route('admin.roles.edit', $role),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function all_admin_permission_routes_are_protected(): void
    {
        $permission = Permission::create(['name' => 'test']);

        $routes = [
            route('admin.permissions.index'),
            route('admin.permissions.create'),
            route('admin.permissions.show', $permission),
            route('admin.permissions.edit', $permission),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Clear permission cache after each test.
     */
    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }

    /**
     * DASHBOARD AUTHORIZATION TESTS
     */

    /** @test */
    public function guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_without_dashboard_permission_cannot_access_dashboard(): void
    {
        // Create a user without any permissions
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_with_dashboard_permission_can_access_dashboard(): void
    {
        $viewDashboardPermission = Permission::firstOrCreate(['name' => 'view dashboard']);

        $this->adminWithoutPermission->givePermissionTo($viewDashboardPermission);

        $response = $this->actingAs($this->adminWithoutPermission)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_can_always_access_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }
}
