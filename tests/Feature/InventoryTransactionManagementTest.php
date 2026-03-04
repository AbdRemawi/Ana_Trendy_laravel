<?php

namespace Tests\Feature;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Inventory Transaction Management Test Suite.
 *
 * Tests for inventory transaction CRUD operations and stock tracking.
 */
class InventoryTransactionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;
    protected Permission $manageProductsPermission;
    protected Permission $viewProductsPermission;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('spatie.permission.cache');

        $this->manageProductsPermission = Permission::firstOrCreate([
            'name' => 'manage products',
            'guard_name' => 'web',
        ]);

        $this->viewProductsPermission = Permission::firstOrCreate([
            'name' => 'view products',
            'guard_name' => 'web',
        ]);

        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageProductsPermission, $this->viewProductsPermission]);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function can_create_supply_transaction(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'supply',
                'quantity' => 100,
                'notes' => 'Initial stock',
            ]);

        $response->assertRedirect(route('admin.inventory.index'));

        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 100,
        ]);
    }

    /** @test */
    public function can_create_sale_transaction(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 10,
                'notes' => 'Customer purchase',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 10,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_transaction(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'supply',
                'quantity' => 50,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function transaction_type_must_be_valid(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'invalid_type',
                'quantity' => 10,
            ]);

        $response->assertSessionHasErrors(['type']);
    }

    /** @test */
    public function quantity_must_be_positive_for_supply(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'supply',
                'quantity' => -10,
            ]);

        $response->assertSessionHasErrors(['quantity']);
    }

    /** @test */
    public function can_update_transaction(): void
    {
        $transaction = InventoryTransaction::factory()->supply()->create();

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.inventory.update', $transaction), [
                'quantity' => 150,
                'notes' => 'Updated quantity',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('inventory_transactions', [
            'id' => $transaction->id,
            'quantity' => 150,
        ]);
    }

    /** @test */
    public function can_delete_transaction(): void
    {
        $transaction = InventoryTransaction::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.inventory.destroy', $transaction));

        $response->assertRedirect();

        $this->assertDatabaseMissing('inventory_transactions', [
            'id' => $transaction->id,
        ]);
    }

    /** @test */
    public function can_view_inventory_transactions_list(): void
    {
        InventoryTransaction::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewProductsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.inventory.index'));

        $response->assertStatus(200);
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function can_filter_transactions_by_type(): void
    {
        $product = Product::factory()->create();

        InventoryTransaction::factory()->for($product)->supply()->create();
        InventoryTransaction::factory()->for($product)->sale()->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.inventory.index', ['type' => 'supply']));

        $response->assertStatus(200);
        $transactions = $response->viewData('transactions');
        $this->assertEquals(1, $transactions->count());
        $this->assertEquals('supply', $transactions->first()->type);
    }

    /** @test */
    public function can_view_product_inventory_history(): void
    {
        $product = Product::factory()->create();
        InventoryTransaction::factory()->for($product)->count(3)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewProductsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.inventory.by-product', $product->id));

        $response->assertStatus(200);
        $response->assertViewHas('product');
        $response->assertViewHas('transactions');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_inventory(): void
    {
        $response = $this->get(route('admin.inventory.index'));
        $response->assertRedirect(route('login'));
    }

    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
