<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * User Management Test Suite.
 *
 * Comprehensive tests for User CRUD operations, authorization,
 * validation, mobile number format, and security measures.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageUsersPermission;

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
        $this->manageUsersPermission = Permission::firstOrCreate([
            'name' => 'manage users',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo($this->manageUsersPermission);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo($this->manageUsersPermission);

        // Create affiliate role
        Role::firstOrCreate([
            'name' => 'affiliate',
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
    public function super_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'email' => 'test@example.com',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'mobile' => '0791234567',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0781234567',
                'email' => 'test2@example.com',
                'password' => 'Password123!',
                'role' => 'affiliate',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'mobile' => '0781234567',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_user(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('users', [
            'mobile' => '0791234567',
        ]);
    }

    /** @test */
    public function mobile_validation_accepts_valid_formats(): void
    {
        $validMobiles = ['0781234567', '0791234567', '0771234567'];

        foreach ($validMobiles as $mobile) {
            $response = $this->actingAs($this->superAdmin)
                ->post(route('admin.users.store'), [
                    'name' => 'Test User',
                    'mobile' => $mobile,
                    'password' => 'Password123!',
                    'role' => 'admin',
                    'status' => 'active',
                ]);

            $response->assertSessionHasNoErrors();
        }
    }

    /** @test */
    public function mobile_validation_rejects_invalid_formats(): void
    {
        $invalidMobiles = [
            '0761234567', // Wrong prefix
            '078123456',  // Too short
            '07812345678', // Too long
            '1234567890',  // Wrong prefix
            'abcdefghij',  // Non-numeric
            '078-1234567', // Contains dash
        ];

        foreach ($invalidMobiles as $mobile) {
            $response = $this->actingAs($this->superAdmin)
                ->post(route('admin.users.store'), [
                    'name' => 'Test User',
                    'mobile' => $mobile,
                    'password' => 'Password123!',
                    'role' => 'admin',
                    'status' => 'active',
                ]);

            $response->assertSessionHasErrors(['mobile']);
        }
    }

    /** @test */
    public function duplicate_mobile_is_rejected(): void
    {
        User::factory()->create(['mobile' => '0791234567']);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['mobile']);
    }

    /** @test */
    public function password_is_hashed(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $user = User::where('mobile', '0791234567')->first();
        $this->assertNotEquals('Password123!', $user->password);
        $this->assertTrue(Hash::check('Password123!', $user->password));
    }

    /** @test */
    public function role_assignment_works(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $user = User::where('mobile', '0791234567')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    /** @test */
    public function can_update_user_without_changing_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123!')]);
        $user->assignRole('admin');
        $oldPassword = $user->password;

        $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'mobile' => '0791234567',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals($oldPassword, $user->password);
    }

    /** @test */
    public function can_update_user_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'mobile' => '0791234567',
                'role' => 'affiliate',
                'status' => 'active',
            ]);

        $user->refresh();
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('affiliate'));
    }

    /** @test */
    public function cannot_modify_own_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $this->admin), [
                'name' => $this->admin->name,
                'mobile' => '0791234567',
                'role' => 'affiliate',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->admin->refresh();
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    /** @test */
    public function cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function cannot_delete_super_admin_user(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $this->superAdmin));

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id,
        ]);
    }

    /** @test */
    public function cannot_assign_super_admin_role_without_being_super_admin(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'super_admin',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('users', [
            'mobile' => '0791234567',
        ]);
    }

    /** @test */
    public function super_admin_can_assign_super_admin_role(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'password' => 'Password123!',
                'role' => 'super_admin',
                'status' => 'active',
            ]);

        $response->assertSessionHasNoErrors();

        $user = User::where('mobile', '0791234567')->first();
        $this->assertTrue($user->hasRole('super_admin'));
    }

    /** @test */
    public function email_is_nullable(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'email' => null,
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'mobile' => '0791234567',
            'email' => null,
        ]);
    }

    /** @test */
    public function email_must_be_valid_if_provided(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'mobile' => '0791234567',
                'email' => 'invalid-email',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_user_management(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_view_users_list(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
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
