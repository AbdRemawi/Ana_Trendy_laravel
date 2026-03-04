<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Product Initial Inventory Test Suite.
 *
 * Tests for product creation with initial inventory functionality.
 * Validates that:
 * - Initial inventory is required when creating a product
 * - Inventory transaction is created with type='supply'
 * - Zero quantity is allowed
 * - Negative quantity is rejected
 * - Transaction rollback on failure
 */
class ProductInitialInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Permission $manageProductsPermission;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('spatie.permission.cache');

        $this->manageProductsPermission = Permission::firstOrCreate([
            'name' => 'manage products',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($this->manageProductsPermission);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        Storage::fake('public');
    }

    /** @test */
    public function product_can_be_created_with_initial_inventory(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '15.00',
                'sale_price' => '25.00',
                'offer_price' => null,
                'status' => 'active',
                'initial_quantity' => 50,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        // Assert product was created
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $product = Product::where('name', 'Test Product')->first();

        // Assert inventory transaction was created
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'type' => InventoryTransaction::TYPE_SUPPLY,
            'quantity' => 50,
        ]);

        // Assert stock quantity is correct
        $this->assertEquals(50, $product->stock_quantity);
    }

    /** @test */
    public function inventory_transaction_type_is_supply_on_creation(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '15.00',
                'sale_price' => '25.00',
                'status' => 'active',
                'initial_quantity' => 100,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $product = Product::where('name', 'Test Product')->first();
        $transaction = $product->inventoryTransactions()->first();

        $this->assertNotNull($transaction);
        $this->assertEquals(InventoryTransaction::TYPE_SUPPLY, $transaction->type);
        $this->assertEquals(100, $transaction->quantity);
        $this->assertEquals($product->id, $transaction->product_id);
    }

    /** @test */
    public function zero_initial_quantity_is_allowed(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Zero Stock Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'initial_quantity' => 0,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'Zero Stock Product')->first();
        $this->assertEquals(0, $product->stock_quantity);

        // No transaction should be created for zero quantity
        $this->assertEquals(0, $product->inventoryTransactions()->count());
    }

    /** @test */
    public function negative_initial_quantity_is_rejected(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Invalid Product',
                'size' => 'LG',
                'gender' => 'female',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'initial_quantity' => -10,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors(['initial_quantity']);

        $this->assertDatabaseMissing('products', [
            'name' => 'Invalid Product',
        ]);
    }

    /** @test */
    public function initial_quantity_must_be_integer(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Invalid Product',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'initial_quantity' => 10.5,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors(['initial_quantity']);
    }

    /** @test */
    public function initial_quantity_is_required(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors(['initial_quantity']);
    }

    /** @test */
    public function transaction_rolls_back_on_image_upload_failure(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        // Create an invalid file (not an image)
        $invalidFile = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Failed Product',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'initial_quantity' => 50,
                'images' => [$invalidFile],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors();

        // Assert nothing was created
        $this->assertDatabaseMissing('products', [
            'name' => 'Failed Product',
        ]);

        $this->assertDatabaseMissing('inventory_transactions', [
            'quantity' => 50,
        ]);
    }

    /** @test */
    public function product_images_and_inventory_created_in_same_transaction(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $images = [
            \Illuminate\Http\UploadedFile::fake()->image('product1.jpg'),
            \Illuminate\Http\UploadedFile::fake()->image('product2.jpg'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Multi Image Product',
                'size' => 'LG',
                'gender' => 'female',
                'cost_price' => '20.00',
                'sale_price' => '40.00',
                'status' => 'active',
                'initial_quantity' => 25,
                'images' => $images,
                'primary_image' => 0,
            ]);

        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'Multi Image Product')->first();

        // Assert both images were saved
        $this->assertEquals(2, $product->images()->count());

        // Assert inventory transaction was created
        $this->assertEquals(1, $product->inventoryTransactions()->count());
        $this->assertEquals(25, $product->stock_quantity);

        // Assert primary image is set correctly
        $this->assertTrue($product->images()->where('is_primary', true)->exists());
    }

    /** @test */
    public function large_initial_quantity_is_handled_correctly(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $largeQuantity = 999999;

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Bulk Product',
                'size' => 'MD',
                'gender' => 'unisex',
                'cost_price' => '5.00',
                'sale_price' => '10.00',
                'status' => 'active',
                'initial_quantity' => $largeQuantity,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'Bulk Product')->first();
        $this->assertEquals($largeQuantity, $product->stock_quantity);
    }

    /** @test */
    public function inventory_transaction_has_correct_notes(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('product.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'initial_quantity' => 30,
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $product = Product::where('name', 'Test Product')->first();
        $transaction = $product->inventoryTransactions()->first();

        $this->assertNotNull($transaction->notes);
        $this->assertStringContainsString('Initial stock', $transaction->notes);
    }

    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
