<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * City Management Test Suite.
 *
 * Comprehensive tests for City CRUD operations, authorization,
 * validation, and security measures.
 */
class CityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageCitiesPermission;
    protected Permission $viewCitiesPermission;

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
        $this->manageCitiesPermission = Permission::firstOrCreate([
            'name' => 'manage cities',
            'guard_name' => 'web',
        ]);

        $this->viewCitiesPermission = Permission::firstOrCreate([
            'name' => 'view cities',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageCitiesPermission, $this->viewCitiesPermission]);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo([$this->manageCitiesPermission, $this->viewCitiesPermission]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_create_city(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.store'), [
                'name' => 'Amman',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.cities.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cities', [
            'name' => 'Amman',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_city(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.cities.store'), [
                'name' => 'Zarqa',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.cities.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cities', [
            'name' => 'Zarqa',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_city(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.cities.store'), [
                'name' => 'Irbid',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('cities', [
            'name' => 'Irbid',
        ]);
    }

    /** @test */
    public function duplicate_city_name_is_rejected(): void
    {
        City::factory()->create(['name' => 'Amman', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.store'), [
                'name' => 'Amman',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function city_name_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.store'), [
                'name' => '',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function can_create_inactive_city(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.store'), [
                'name' => 'Aqaba',
                'is_active' => '0',
            ]);

        $this->assertDatabaseHas('cities', [
            'name' => 'Aqaba',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function can_update_city(): void
    {
        $city = City::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.cities.update', $city), [
                'name' => 'New Name',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.cities.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_city(): void
    {
        $city = City::factory()->create(['name' => 'Test City']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.cities.update', $city), [
                'name' => 'Updated Name',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'name' => 'Test City',
        ]);
    }

    /** @test */
    public function can_delete_city(): void
    {
        $city = City::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.cities.destroy', $city));

        $response->assertRedirect(route('admin.cities.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('cities', [
            'id' => $city->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_city(): void
    {
        $city = City::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.cities.destroy', $city));

        $response->assertStatus(403);

        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
        ]);
    }

    /** @test */
    public function can_restore_city(): void
    {
        $city = City::factory()->create();
        $city->delete();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.restore', $city->id));

        $response->assertRedirect(route('admin.cities.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function can_toggle_city_status(): void
    {
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.cities.toggle-status', $city));

        $response->assertStatus(200);
        $this->assertDatabaseHas('cities', [
            'id' => $city->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_cities_list(): void
    {
        City::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCitiesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.cities.index'));

        $response->assertStatus(200);
        $response->assertViewHas('cities');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_cities_list(): void
    {
        City::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.cities.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_view_city_details(): void
    {
        $city = City::factory()->create(['name' => 'Amman']);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCitiesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.cities.show', $city));

        $response->assertStatus(200);
        $response->assertViewHas('city');
        $response->assertSee('Amman');
    }

    /** @test */
    public function can_search_cities_by_name(): void
    {
        City::factory()->create(['name' => 'Amman']);
        City::factory()->create(['name' => 'Zarqa']);
        City::factory()->create(['name' => 'Irbid']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cities.index', ['search' => 'Amman']));

        $response->assertStatus(200);
        $response->assertViewHas('cities');
        $this->assertEquals(1, $response->viewData('cities')->count());
    }

    /** @test */
    public function can_filter_cities_by_status(): void
    {
        City::factory()->create(['name' => 'Active City', 'is_active' => true]);
        City::factory()->create(['name' => 'Inactive City', 'is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cities.index', ['status' => 'active']));

        $response->assertStatus(200);
        $cities = $response->viewData('cities');
        $this->assertEquals(1, $cities->count());
        $this->assertEquals('Active City', $cities->first()->name);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_cities(): void
    {
        $response = $this->get(route('admin.cities.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.cities.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.cities.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_edit_form(): void
    {
        $city = City::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cities.edit', $city));

        $response->assertStatus(200);
        $response->assertViewHas('city');
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
