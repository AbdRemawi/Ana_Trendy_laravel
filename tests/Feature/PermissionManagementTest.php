<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Permission Management Test Suite.
 *
 * Comprehensive tests for Permission CRUD operations, authorization,
 * validation, and role assignment prevention.
 */
class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $managePermissionsPermission;

    /**
     * Set up test fixtures.
     * Creates users with different roles and necessary permissions.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear Spatie permission cache before each test
        Cache::forget('spatie.permission.cache');

        // Create permissions
        $this->managePermissionsPermission = Permission::firstOrCreate([
            'name' => 'manage permissions',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo($this->managePermissionsPermission);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_create_permission(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage products',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage_products',  // Converted to snake_case
        ]);
    }

    /** @test */
    public function permission_name_is_converted_to_snake_case(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'Manage Products',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage_products',
        ]);
    }

    /** @test */
    public function permission_name_with_spaces_is_converted(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'view user reports',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('permissions', [
            'name' => 'view_user_reports',
        ]);
    }

    /** @test */
    public function admin_without_permission_cannot_create_permission(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage products',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'manage products',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_permission(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage products',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'manage products',
        ]);
    }

    /** @test */
    public function permission_name_must_be_unique(): void
    {
        Permission::create(['name' => 'manage_products']);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage products',  // Will be converted to manage_products
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function permission_name_must_be_valid_format(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'Invalid-Permission-Name!',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function permission_name_must_be_at_least_2_characters(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'a',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function super_admin_can_view_permissions_list(): void
    {
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'view orders']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
        $response->assertSee('manage products');
        $response->assertSee('view orders');
    }

    /** @test */
    public function permissions_list_shows_role_count(): void
    {
        $permission = Permission::create(['name' => 'manage_products']);

        $role1 = Role::firstOrCreate(['name' => 'test_admin_' . uniqid()]);
        $role2 = Role::firstOrCreate(['name' => 'test_manager_' . uniqid()]);

        $role1->givePermissionTo($permission);
        $role2->givePermissionTo($permission);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('permissions');

        $permissions = $response->viewData('permissions');
        $productPermission = $permissions->firstWhere('name', 'manage_products');
        $this->assertEquals(2, $productPermission->roles_count);
    }

    /** @test */
    public function super_admin_can_view_single_permission(): void
    {
        $permission = Permission::create(['name' => 'manage_products']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.permissions.show', $permission));

        $response->assertStatus(200);
        $response->assertViewHas('permission');
        $response->assertSee('manage_products');
    }

    /** @test */
    public function super_admin_can_update_permission(): void
    {
        $permission = Permission::create(['name' => 'edit products']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'manage products',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'manage_products',  // Converted to snake_case
        ]);
    }

    /** @test */
    public function super_admin_can_delete_permission(): void
    {
        $permission = Permission::create(['name' => 'unused permission']);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', [
            'name' => 'unused permission',
        ]);
    }

    /** @test */
    public function cannot_delete_permission_assigned_to_role(): void
    {
        $permission = Permission::create(['name' => 'manage_products_' . uniqid()]);

        $role = Role::firstOrCreate(['name' => 'test_manager_' . uniqid()]);
        $role->givePermissionTo($permission);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('permissions', [
            'name' => $permission->name,
        ]);
    }

    /** @test */
    public function cannot_delete_permission_assigned_to_multiple_roles(): void
    {
        $permission = Permission::create(['name' => 'manage_products_' . uniqid()]);

        $role1 = Role::firstOrCreate(['name' => 'test_admin_' . uniqid()]);
        $role2 = Role::firstOrCreate(['name' => 'test_manager_' . uniqid()]);

        $role1->givePermissionTo($permission);
        $role2->givePermissionTo($permission);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('permissions', [
            'name' => $permission->name,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_permission_management(): void
    {
        $response = $this->get(route('admin.permissions.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function permission_view_includes_assigned_roles(): void
    {
        $permission = Permission::create(['name' => 'manage_products_' . uniqid()]);

        $role1 = Role::firstOrCreate(['name' => 'test_admin_' . uniqid()]);
        $role2 = Role::firstOrCreate(['name' => 'test_manager_' . uniqid()]);

        $role1->givePermissionTo($permission);
        $role2->givePermissionTo($permission);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.permissions.show', $permission));

        $response->assertStatus(200);
        $response->assertViewHas('permission');

        $permissionData = $response->viewData('permission');
        $this->assertTrue($permissionData->relationLoaded('roles'));
        $this->assertCount(2, $permissionData->roles);
    }

    /**
     * Clear permission cache after each test.
     */
    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
