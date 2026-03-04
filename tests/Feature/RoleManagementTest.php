<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Role Management Test Suite.
 *
 * Comprehensive tests for Role CRUD operations, authorization,
 * validation, and permission synchronization.
 */
class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageRolesPermission;

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
        $this->manageRolesPermission = Permission::firstOrCreate([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo($this->manageRolesPermission);

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
    public function super_admin_can_create_role(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), [
                'name' => 'editor',
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'editor',
        ]);
    }

    /** @test */
    public function admin_without_permission_cannot_create_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name' => 'editor',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('roles', [
            'name' => 'editor',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_role(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.roles.store'), [
                'name' => 'editor',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('roles', [
            'name' => 'editor',
        ]);
    }

    /** @test */
    public function role_name_must_be_unique(): void
    {
        Role::create(['name' => 'editor']);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), [
                'name' => 'editor',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function role_name_must_be_snake_case(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), [
                'name' => 'Invalid-Role-Name',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function role_name_must_be_at_least_2_characters(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), [
                'name' => 'a',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function super_admin_can_view_roles_list(): void
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'moderator']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');
        $response->assertSee('editor');
        $response->assertSee('moderator');
    }

    /** @test */
    public function super_admin_can_view_single_role(): void
    {
        $role = Role::create(['name' => 'editor']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.show', $role));

        $response->assertStatus(200);
        $response->assertViewHas('role');
        $response->assertSee('editor');
    }

    /** @test */
    public function super_admin_can_update_role_name(): void
    {
        $role = Role::create(['name' => 'editor']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'content_editor',
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'content_editor',
        ]);
    }

    /** @test */
    public function super_admin_can_sync_permissions_to_role(): void
    {
        $role = Role::create(['name' => 'editor']);

        $permission1 = Permission::create(['name' => 'edit articles']);
        $permission2 = Permission::create(['name' => 'publish articles']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'editor',
                'permissions' => [$permission1->name, $permission2->name],
            ]);

        $response->assertSessionHasNoErrors();

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('edit articles'));
        $this->assertTrue($role->hasPermissionTo('publish articles'));
    }

    /** @test */
    public function super_admin_can_delete_role(): void
    {
        $role = Role::create(['name' => 'editor']);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', [
            'name' => 'editor',
        ]);
    }

    /** @test */
    public function cannot_delete_super_admin_role(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.roles.destroy', $superAdminRole));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', [
            'name' => 'super_admin',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_role_management(): void
    {
        $response = $this->get(route('admin.roles.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function role_list_includes_permission_count(): void
    {
        $role = Role::create(['name' => 'editor']);

        $permission1 = Permission::create(['name' => 'edit articles']);
        $permission2 = Permission::create(['name' => 'publish articles']);

        $role->givePermissionTo([$permission1, $permission2]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roles');

        $roles = $response->viewData('roles');
        $editorRole = $roles->firstWhere('name', 'editor');
        $this->assertEquals(2, $editorRole->permissions_count);
    }

    /** @test */
    public function super_admin_can_get_role_via_api(): void
    {
        $role = Role::create(['name' => 'editor']);
        $permission = Permission::create(['name' => 'edit articles']);
        $role->givePermissionTo($permission);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.get', $role));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $role->id,
            'name' => 'editor',
            'permissions' => ['edit articles'],
        ]);
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
