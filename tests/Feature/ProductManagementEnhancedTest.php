<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Enhanced Product Management Test Suite.
 *
 * Tests for:
 * - Product + Images unified form
 * - Primary image requirements
 * - Inventory non-negative validation
 * - Size enum changes (S, MD, LG)
 * - Transaction safety
 */
class ProductManagementEnhancedTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageProductsPermission;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('spatie.permission.cache');

        Storage::fake('public');

        $this->manageProductsPermission = Permission::firstOrCreate([
            'name' => 'manage products',
            'guard_name' => 'web',
        ]);

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo($this->manageProductsPermission);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($this->manageProductsPermission);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function product_requires_at_least_one_image(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'images' => [], // No images
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors('images');
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function product_requires_primary_image_selection(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image1 = \Illuminate\Http\UploadedFile::fake()->image('test1.jpg');
        $image2 = \Illuminate\Http\UploadedFile::fake()->image('test2.jpg');

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'images' => [$image1, $image2],
                'primary_image' => null, // No primary selected
            ]);

        $response->assertSessionHasErrors('primary_image');
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function product_created_with_images_and_primary_selection(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image1 = \Illuminate\Http\UploadedFile::fake()->image('test1.jpg');
        $image2 = \Illuminate\Http\UploadedFile::fake()->image('test2.jpg');
        $image3 = \Illuminate\Http\UploadedFile::fake()->image('test3.jpg');

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'images' => [$image1, $image2, $image3],
                'primary_image' => 1, // Second image is primary
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'size' => 'MD',
        ]);

        $product = Product::where('name', 'Test Product')->first();
        $this->assertEquals(3, $product->images()->count());

        // Verify only one primary image exists
        $primaryCount = $product->images()->where('is_primary', true)->count();
        $this->assertEquals(1, $primaryCount);

        // Verify the second image is primary
        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertEquals(1, $primaryImage->sort_order);
    }

    /** @test */
    public function exactly_one_primary_image_exists_after_creation(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image1 = \Illuminate\Http\UploadedFile::fake()->image('test1.jpg');
        $image2 = \Illuminate\Http\UploadedFile::fake()->image('test2.jpg');

        $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'LG',
                'gender' => 'female',
                'cost_price' => '15.00',
                'sale_price' => '30.00',
                'status' => 'active',
                'images' => [$image1, $image2],
                'primary_image' => 0,
            ]);

        $product = Product::where('name', 'Test Product')->first();

        // Exactly one primary image
        $this->assertEquals(1, $product->images()->where('is_primary', true)->count());

        // Exactly one non-primary image
        $this->assertEquals(1, $product->images()->where('is_primary', false)->count());
    }

    /** @test */
    public function size_validation_accepts_only_new_values(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        // Test valid sizes
        foreach (['S', 'MD', 'LG'] as $validSize) {
            $response = $this->actingAs($this->superAdmin)
                ->post(route('admin.products.store'), [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'name' => "Test Product $validSize",
                    'size' => $validSize,
                    'gender' => 'male',
                    'cost_price' => '10.00',
                    'sale_price' => '20.00',
                    'status' => 'active',
                    'images' => [\Illuminate\Http\UploadedFile::fake()->image('test.jpg')],
                    'primary_image' => 0,
                ]);

            $response->assertSessionHasNoErrors();
        }

        // Test invalid sizes
        foreach (['M', 'L', 'XL', 'XXL', 'XXXL'] as $invalidSize) {
            $response = $this->actingAs($this->superAdmin)
                ->post(route('admin.products.store'), [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'name' => "Test Product $invalidSize",
                    'size' => $invalidSize,
                    'gender' => 'male',
                    'cost_price' => '10.00',
                    'sale_price' => '20.00',
                    'status' => 'active',
                    'images' => [\Illuminate\Http\UploadedFile::fake()->image('test.jpg')],
                    'primary_image' => 0,
                ]);

            $response->assertSessionHasErrors('size');
        }
    }

    /** @test */
    public function inventory_transaction_prevented_when_would_go_negative(): void
    {
        $product = Product::factory()->create();
        InventoryTransaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 5,
        ]);

        // Current stock: 5
        $this->assertEquals(5, $product->stock_quantity);

        // Try to sell 10 (would result in -5)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 10,
                'notes' => 'Test sale',
            ]);

        $response->assertSessionHasErrors('quantity');

        // Verify transaction was NOT created
        $this->assertEquals(5, $product->refresh()->stock_quantity);
    }

    /** @test */
    public function inventory_transaction_allowed_when_stock_sufficient(): void
    {
        $product = Product::factory()->create();
        InventoryTransaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 10,
        ]);

        // Current stock: 10
        $this->assertEquals(10, $product->stock_quantity);

        // Sell 5 (would result in 5, which is >= 0)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 5,
                'notes' => 'Test sale',
            ]);

        $response->assertSessionHasNoErrors();

        // Verify transaction was created
        $this->assertEquals(5, $product->refresh()->stock_quantity);
    }

    /** @test */
    public function damage_transaction_prevented_when_would_go_negative(): void
    {
        $product = Product::factory()->create();
        InventoryTransaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 3,
        ]);

        // Current stock: 3
        $this->assertEquals(3, $product->stock_quantity);

        // Try to damage 5 (would result in -2)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'damage',
                'quantity' => 5,
                'notes' => 'Test damage',
            ]);

        $response->assertSessionHasErrors('quantity');

        // Verify stock unchanged
        $this->assertEquals(3, $product->refresh()->stock_quantity);
    }

    /** @test */
    public function adjustment_transaction_prevented_when_would_go_negative(): void
    {
        $product = Product::factory()->create();
        InventoryTransaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 5,
        ]);

        // Current stock: 5
        $this->assertEquals(5, $product->stock_quantity);

        // Try to adjust by -10 (would result in -5)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => -10,
                'notes' => 'Test adjustment',
            ]);

        $response->assertSessionHasErrors('quantity');

        // Verify stock unchanged
        $this->assertEquals(5, $product->refresh()->stock_quantity);
    }

    /** @test */
    public function stock_never_goes_below_zero_with_multiple_transactions(): void
    {
        $product = Product::factory()->create();

        // Supply 100
        InventoryTransaction::factory()->create([
            'product_id' => $product->id,
            'type' => 'supply',
            'quantity' => 100,
        ]);

        $this->assertEquals(100, $product->stock_quantity);

        // Sale 30 -> stock: 70
        $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 30,
                'notes' => 'Sale 1',
            ]);

        $this->assertEquals(70, $product->refresh()->stock_quantity);

        // Damage 20 -> stock: 50
        $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'damage',
                'quantity' => 20,
                'notes' => 'Damage 1',
            ]);

        $this->assertEquals(50, $product->refresh()->stock_quantity);

        // Try to sale 60 (would go to -10) -> should fail
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 60,
                'notes' => 'Sale 2 (blocked)',
            ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertEquals(50, $product->refresh()->stock_quantity);

        // Verify final stock is exactly 50 (never went negative)
        $this->assertEquals(50, $product->stock_quantity);
        $this->assertGreaterThanOrEqual(0, $product->stock_quantity);
    }

    /** @test */
    public function unauthorized_user_cannot_create_product_with_images(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function transaction_safety_on_product_creation_failure(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $image = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

        // Attempt with invalid data (sale_price < cost_price)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'S',
                'gender' => 'male',
                'cost_price' => '30.00',
                'sale_price' => '20.00', // Invalid: less than cost
                'status' => 'active',
                'images' => [$image],
                'primary_image' => 0,
            ]);

        $response->assertSessionHasErrors('sale_price');

        // Verify product was NOT created
        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);

        // Verify images were NOT stored
        $this->assertEquals(0, ProductImage::count());
    }

    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
