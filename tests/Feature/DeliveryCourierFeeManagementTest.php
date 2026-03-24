<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\DeliveryCourier;
use App\Models\DeliveryCourierFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Delivery Courier Fee Management Test Suite.
 *
 * Comprehensive tests for Delivery Courier Fee CRUD operations, authorization,
 * validation, unique constraints, and security measures.
 */
class DeliveryCourierFeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageFeesPermission;
    protected Permission $viewFeesPermission;

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
        $this->manageFeesPermission = Permission::firstOrCreate([
            'name' => 'manage delivery courier fees',
            'guard_name' => 'web',
        ]);

        $this->viewFeesPermission = Permission::firstOrCreate([
            'name' => 'view delivery courier fees',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageFeesPermission, $this->viewFeesPermission]);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo([$this->manageFeesPermission, $this->viewFeesPermission]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_create_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'Aramex', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Amman', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '5.500',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-courier-fees.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_courier_fees', [
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'real_fee_amount' => '5.500',
            'currency' => 'JOD',
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'FedEx', 'is_active' => true]);
        $city = City::factory()->create(['name' => 'Zarqa', 'is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '3.250',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-courier-fees.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_courier_fees', [
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '5.500',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function unique_courier_city_combination_is_enforced(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'real_fee_amount' => '5.500',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '6.000',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['delivery_courier_id']);
    }

    /** @test */
    public function courier_id_is_required(): void
    {
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => '',
                'city_id' => $city->id,
                'real_fee_amount' => '5.500',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['delivery_courier_id']);
    }

    /** @test */
    public function city_id_is_required(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => '',
                'real_fee_amount' => '5.500',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['city_id']);
    }

    /** @test */
    public function real_fee_amount_is_required(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['real_fee_amount']);
    }

    /** @test */
    public function currency_is_required(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '5.500',
                'currency' => '',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['currency']);
    }

    /** @test */
    public function fee_amounts_must_have_3_decimal_places(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '5.5', // Missing decimal places
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['real_fee_amount']);
    }

    /** @test */
    public function fee_amounts_must_be_numeric(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => 'invalid',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors(['real_fee_amount']);
    }

    /** @test */
    public function can_create_inactive_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.store'), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '5.500',
                'currency' => 'JOD',
                'is_active' => '0',
            ]);

        $this->assertDatabaseHas('delivery_courier_fees', [
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function can_update_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'real_fee_amount' => '5.500',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.delivery-courier-fees.update', $fee), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '6.000',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.delivery-courier-fees.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('delivery_courier_fees', [
            'id' => $fee->id,
            'real_fee_amount' => '6.000',
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'real_fee_amount' => '5.500',
        ]);

        $originalRealFee = $fee->real_fee_amount;

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.delivery-courier-fees.update', $fee), [
                'delivery_courier_id' => $courier->id,
                'city_id' => $city->id,
                'real_fee_amount' => '6.000',
                'currency' => 'JOD',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('delivery_courier_fees', [
            'id' => $fee->id,
            'real_fee_amount' => $originalRealFee,
        ]);
    }

    /** @test */
    public function can_delete_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.delivery-courier-fees.destroy', $fee));

        $response->assertRedirect(route('admin.delivery-courier-fees.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('delivery_courier_fees', [
            'id' => $fee->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_fee(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.delivery-courier-fees.destroy', $fee));

        $response->assertStatus(403);

        $this->assertDatabaseHas('delivery_courier_fees', [
            'id' => $fee->id,
        ]);
    }

    /** @test */
    public function can_toggle_fee_status(): void
    {
        $courier = DeliveryCourier::factory()->create(['is_active' => true]);
        $city = City::factory()->create(['is_active' => true]);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.delivery-courier-fees.toggle-status', $fee));

        $response->assertStatus(200);
        $this->assertDatabaseHas('delivery_courier_fees', [
            'id' => $fee->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_fees_list(): void
    {
        DeliveryCourierFee::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewFeesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.delivery-courier-fees.index'));

        $response->assertStatus(200);
        $response->assertViewHas('fees');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_fees_list(): void
    {
        DeliveryCourierFee::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.delivery-courier-fees.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_view_fee_details(): void
    {
        $courier = DeliveryCourier::factory()->create(['name' => 'Aramex']);
        $city = City::factory()->create(['name' => 'Amman']);

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
            'real_fee_amount' => '5.500',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewFeesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.delivery-courier-fees.show', $fee));

        $response->assertStatus(200);
        $response->assertViewHas('fee');
        $response->assertSee('5.500');
    }

    /** @test */
    public function can_search_fees_by_courier_or_city(): void
    {
        $courier1 = DeliveryCourier::factory()->create(['name' => 'Aramex']);
        $courier2 = DeliveryCourier::factory()->create(['name' => 'FedEx']);
        $city = City::factory()->create(['name' => 'Amman']);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier1->id,
            'city_id' => $city->id,
        ]);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier2->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-courier-fees.index', ['search' => 'Aramex']));

        $response->assertStatus(200);
        $fees = $response->viewData('fees');
        $this->assertEquals(1, $fees->count());
    }

    /** @test */
    public function can_filter_fees_by_courier(): void
    {
        $courier1 = DeliveryCourier::factory()->create(['name' => 'Aramex']);
        $courier2 = DeliveryCourier::factory()->create(['name' => 'FedEx']);
        $city = City::factory()->create();

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier1->id,
            'city_id' => $city->id,
        ]);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier2->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-courier-fees.index', ['courier_id' => $courier1->id]));

        $response->assertStatus(200);
        $fees = $response->viewData('fees');
        $this->assertEquals(1, $fees->count());
        $this->assertEquals($courier1->id, $fees->first()->delivery_courier_id);
    }

    /** @test */
    public function can_filter_fees_by_city(): void
    {
        $courier = DeliveryCourier::factory()->create();
        $city1 = City::factory()->create(['name' => 'Amman']);
        $city2 = City::factory()->create(['name' => 'Zarqa']);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city1->id,
        ]);

        DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-courier-fees.index', ['city_id' => $city1->id]));

        $response->assertStatus(200);
        $fees = $response->viewData('fees');
        $this->assertEquals(1, $fees->count());
        $this->assertEquals($city1->id, $fees->first()->city_id);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_fees(): void
    {
        $response = $this->get(route('admin.delivery-courier-fees.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-courier-fees.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.delivery-courier-fees.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_edit_form(): void
    {
        $courier = DeliveryCourier::factory()->create();
        $city = City::factory()->create();

        $fee = DeliveryCourierFee::factory()->create([
            'delivery_courier_id' => $courier->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.delivery-courier-fees.edit', $fee));

        $response->assertStatus(200);
        $response->assertViewHas('fee');
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
