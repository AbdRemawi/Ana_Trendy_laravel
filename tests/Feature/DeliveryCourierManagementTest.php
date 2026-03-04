<?php

namespace Tests\Feature;

use App\Models\DeliveryCourier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Delivery Courier Management Test Suite.
 *
 * Comprehensive tests for Delivery Courier CRUD operations, authorization,
 * validation, and security measures.
 */
class DeliveryCourierManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageCouriersPermission;
    protected Permission $viewCouriersPermission;

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
        $this->manageCouriersPermission = Permission::firstOrCreate([
            'name' => 'manage delivery couriers',
            'guard_name' => 'web',
        ]);

        $this->viewCouriersPermission = Permission::firstOrCreate([
            'name' => 'view delivery couriers',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageCouriersPermission, $this->viewCouriersPermission]);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo([$this->manageCouriersPermission, $this->viewCouriersPermission]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_create_courier(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'Aramex',
                'contact_phone' => '+962791234567',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-couriers.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_couriers', [
            'name' => 'Aramex',
            'contact_phone' => '+962791234567',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_courier(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'FedEx',
                'contact_phone' => '+96265123456',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-couriers.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_couriers', [
            'name' => 'FedEx',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_courier(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'DHL',
                'contact_phone' => '+962797654321',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('delivery_couriers', [
            'name' => 'DHL',
        ]);
    }

    /** @test */
    public function duplicate_courier_name_is_rejected(): void
    {
        DeliveryCourier::factory()->create(['name' => 'Aramex', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'Aramex',
                'contact_phone' => '+962791234567',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function courier_name_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => '',
                'contact_phone' => '+962791234567',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function contact_phone_is_optional(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'Aramex',
                'contact_phone' => null,
                'is_active' => '1',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('delivery_couriers', [
            'name' => 'Aramex',
        ]);
    }

    /** @test */
    public function contact_phone_has_max_length(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'Aramex',
                'contact_phone' => str_repeat('1', 21),
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['contact_phone']);
    }

    /** @test */
    public function can_create_inactive_courier(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.store'), [
                'name' => 'UPS',
                'contact_phone' => null,
                'is_active' => '0',
            ]);

        $this->assertDatabaseHas('delivery_couriers', [
            'name' => 'UPS',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function can_update_courier(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.delivery-couriers.update', $courier), [
                'name' => 'New Name',
                'contact_phone' => '+962791234567',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-couriers.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_couriers', [
            'id' => $courier->id,
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_courier(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'Test Courier']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.delivery-couriers.update', $courier), [
                'name' => 'Updated Name',
                'contact_phone' => '+962791234567',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('delivery_couriers', [
            'id' => $courier->id,
            'name' => 'Test Courier',
        ]);
    }

    /** @test */
    public function can_delete_courier(): void
    {
        $courier = DeliveryCourier::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.delivery-couriers.destroy', $courier));

        $response->assertRedirect(route('admin.delivery-couriers.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('delivery_couriers', [
            'id' => $courier->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_courier(): void
    {
        $courier = DeliveryCourier::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.delivery-couriers.destroy', $courier));

        $response->assertStatus(403);

        $this->assertDatabaseHas('delivery_couriers', [
            'id' => $courier->id,
        ]);
    }

    /** @test */
    public function can_restore_courier(): void
    {
        $courier = DeliveryCourier::factory()->create();
        $courier->delete();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.restore', $courier->id));

        $response->assertRedirect(route('admin.delivery-couriers.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_couriers', [
            'id' => $courier->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function can_toggle_courier_status(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-couriers.toggle-status', $courier));

        $response->assertStatus(200);
        $this->assertDatabaseHas('delivery_couriers', [
            'id' => $courier->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_couriers_list(): void
    {
        DeliveryCourier::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCouriersPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.delivery-couriers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('couriers');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_couriers_list(): void
    {
        DeliveryCourier::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.delivery-couriers.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_view_courier_details(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'Aramex']);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCouriersPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.delivery-couriers.show', $courier));

        $response->assertStatus(200);
        $response->assertViewHas('courier');
        $response->assertSee('Aramex');
    }

    /** @test */
    public function can_search_couriers_by_name(): void
    {
        DeliveryCourier::factory()->create(['name' => 'Aramex']);
        DeliveryCourier::factory()->create(['name' => 'FedEx']);
        DeliveryCourier::factory()->create(['name' => 'DHL']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-couriers.index', ['search' => 'Aramex']));

        $response->assertStatus(200);
        $response->assertViewHas('couriers');
        $this->assertEquals(1, $response->viewData('couriers')->count());
    }

    /** @test */
    public function can_filter_couriers_by_status(): void
    {
        DeliveryCourier::factory()->create(['name' => 'Active Courier', 'is_active' => true]);
        DeliveryCourier::factory()->create(['name' => 'Inactive Courier', 'is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-couriers.index', ['status' => 'active']));

        $response->assertStatus(200);
        $couriers = $response->viewData('couriers');
        $this->assertEquals(1, $couriers->count());
        $this->assertEquals('Active Courier', $couriers->first()->name);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_couriers(): void
    {
        $response = $this->get(route('admin.delivery-couriers.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-couriers.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.delivery-couriers.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_edit_form(): void
    {
        $courier = DeliveryCourier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-couriers.edit', $courier));

        $response->assertStatus(200);
        $response->assertViewHas('courier');
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
