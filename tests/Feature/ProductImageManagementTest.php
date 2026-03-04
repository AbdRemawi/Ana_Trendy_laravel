<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Product Image Management Test Suite.
 *
 * Tests for product image CRUD operations with the NEW simplified approach:
 * - Create Product: First image auto-becomes primary, redirects to edit page
 * - Edit Product: Single radio button group for ALL images (existing + new)
 * - Backend enforces exactly one primary image atomically
 */
class ProductImageManagementTest extends TestCase
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

        Storage::fake('public');
    }

    /** @test */
    public function can_create_product_with_images_first_image_auto_becomes_primary(): void
    {
        Storage::fake('public');

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $images = [
            \Illuminate\Http\UploadedFile::fake()->image('product1.jpg', 800, 600)->size(1000),
            \Illuminate\Http\UploadedFile::fake()->image('product2.jpg', 800, 600)->size(1000),
            \Illuminate\Http\UploadedFile::fake()->image('product3.jpg', 800, 600)->size(1000),
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => 100.00,
                'sale_price' => 150.00,
                'status' => 'active',
                'initial_quantity' => 10,
                'images' => $images,
                // NO primary_image field - first image auto-becomes primary
            ]);

        // Should redirect to edit page (not index)
        $response->assertRedirect(route('admin.products.edit', Product::where('name', 'Test Product')->first()->id));

        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product);
        $this->assertCount(3, $product->images);

        // Verify first image (sort_order 0) is primary
        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
        $this->assertEquals(0, $primaryImage->sort_order);

        // Verify only one primary image
        $this->assertEquals(1, $product->images()->where('is_primary', true)->count());
    }

    /** @test */
    public function can_create_product_with_single_image_auto_becomes_primary(): void
    {
        Storage::fake('public');

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $images = [
            \Illuminate\Http\UploadedFile::fake()->image('product1.jpg', 800, 600)->size(1000),
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => 100.00,
                'sale_price' => 150.00,
                'status' => 'active',
                'initial_quantity' => 10,
                'images' => $images,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.products.edit', Product::where('name', 'Test Product')->first()->id));

        $product = Product::where('name', 'Test Product')->first();
        $this->assertCount(1, $product->images);

        // Verify the single image is primary
        $image = $product->images->first();
        $this->assertTrue($image->is_primary);
    }

    /** @test */
    public function cannot_create_product_without_images(): void
    {
        Storage::fake('public');

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => 100.00,
                'sale_price' => 150.00,
                'status' => 'active',
                'initial_quantity' => 10,
                'images' => [], // No images
            ]);

        $response->assertSessionHasErrors(['images']);
        $this->assertNull(Product::where('name', 'Test Product')->first());
    }

    /** @test */
    public function can_update_product_primary_image_among_existing_images(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        // Create existing images
        $existingImage1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        $existingImage2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);
        $existingImage3 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'gender' => $product->gender,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'images' => [], // No new images
                'primary_image_id' => $existingImage2->id, // Make image2 primary
                'remove_images' => [],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();

        // Verify only one primary image exists
        $this->assertEquals(1, $product->images()->where('is_primary', true)->count());

        // Verify image2 is now primary
        $existingImage2->refresh();
        $this->assertTrue($existingImage2->is_primary);

        // Verify image1 is no longer primary
        $existingImage1->refresh();
        $this->assertFalse($existingImage1->is_primary);

        // Verify image3 is still not primary
        $existingImage3->refresh();
        $this->assertFalse($existingImage3->is_primary);
    }

    /** @test */
    public function can_update_product_with_new_images_and_select_primary_from_existing(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        // Create existing images
        $existingImage1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        $existingImage2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        // New images to upload
        $newImages = [
            \Illuminate\Http\UploadedFile::fake()->image('new1.jpg', 800, 600)->size(1000),
            \Illuminate\Http\UploadedFile::fake()->image('new2.jpg', 800, 600)->size(1000),
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Updated Product',
                'description' => 'Updated description',
                'size' => 'LG',
                'gender' => 'female',
                'cost_price' => 120.00,
                'sale_price' => 180.00,
                'status' => 'active',
                'images' => $newImages,
                'primary_image_id' => $existingImage2->id, // Keep existing image as primary
                'remove_images' => [],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Updated Product', $product->name);

        // Should have 4 images total (2 existing + 2 new)
        $this->assertCount(4, $product->images);

        // Verify only one primary image exists
        $this->assertEquals(1, $product->images()->where('is_primary', true)->count());

        // Verify existingImage2 is still primary
        $existingImage2->refresh();
        $this->assertTrue($existingImage2->is_primary);
    }

    /** @test */
    public function can_remove_image_on_edit(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $image1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $image2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'gender' => $product->gender,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'images' => [],
                'primary_image_id' => $image1->id,
                'remove_images' => [$image2->id],
            ]);

        $response->assertSessionHasNoErrors();

        // Image should be deleted
        $this->assertDatabaseMissing('product_images', ['id' => $image2->id]);

        // First image should still be primary
        $image1->refresh();
        $this->assertTrue($image1->is_primary);
    }

    /** @test */
    public function cannot_update_product_if_removing_primary_image_without_selecting_new_one(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $image1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $image2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'gender' => $product->gender,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'images' => [],
                'primary_image_id' => $image1->id, // Trying to keep image1 as primary
                'remove_images' => [$image1->id], // But also removing it!
            ]);

        $response->assertSessionHasErrors(['primary_image_id']);

        // Verify product was not updated
        $this->assertDatabaseHas('product_images', ['id' => $image1->id]);
        $this->assertDatabaseHas('product_images', ['id' => $image2->id]);
    }

    /** @test */
    public function cannot_update_product_if_resulting_in_zero_images(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $image1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $image2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'gender' => $product->gender,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'images' => [], // No new images
                'remove_images' => [$image1->id, $image2->id], // Removing all images
            ]);

        $response->assertSessionHasErrors(['images']);

        // Verify images still exist
        $this->assertDatabaseHas('product_images', ['id' => $image1->id]);
        $this->assertDatabaseHas('product_images', ['id' => $image2->id]);
    }

    /** @test */
    public function system_enforces_single_primary_image_automatically(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        // Create existing images with image1 as primary
        $existingImage1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $existingImage2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        // Change primary to image2
        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => $product->name,
                'description' => $product->description,
                'size' => $product->size,
                'gender' => $product->gender,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'status' => $product->status,
                'images' => [],
                'primary_image_id' => $existingImage2->id, // Make image2 primary
                'remove_images' => [],
            ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();
        $existingImage1->refresh();

        // Verify only ONE primary image exists
        $primaryCount = $product->images()->where('is_primary', true)->count();
        $this->assertEquals(1, $primaryCount);

        // The old primary should no longer be primary
        $this->assertFalse($existingImage1->is_primary);

        // The new primary should be image2
        $existingImage2->refresh();
        $this->assertTrue($existingImage2->is_primary);
    }

    /** @test */
    public function atomic_primary_enforcement_on_create_with_multiple_images(): void
    {
        Storage::fake('public');

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $images = [
            \Illuminate\Http\UploadedFile::fake()->image('product1.jpg', 800, 600)->size(1000),
            \Illuminate\Http\UploadedFile::fake()->image('product2.jpg', 800, 600)->size(1000),
            \Illuminate\Http\UploadedFile::fake()->image('product3.jpg', 800, 600)->size(1000),
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => 100.00,
                'sale_price' => 150.00,
                'status' => 'active',
                'initial_quantity' => 10,
                'images' => $images,
            ]);

        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product);

        // Verify exactly ONE primary image
        $primaryCount = $product->images()->where('is_primary', true)->count();
        $this->assertEquals(1, $primaryCount);

        // Verify the first image (index 0) is primary
        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertEquals(0, $primaryImage->sort_order);

        // Verify other images are not primary
        $nonPrimaryImages = $product->images()->where('is_primary', false);
        $this->assertCount(2, $nonPrimaryImages);
    }

    /** @test */
    public function regular_user_cannot_create_product(): void
    {
        Storage::fake('public');

        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $images = [
            \Illuminate\Http\UploadedFile::fake()->image('product1.jpg', 800, 600)->size(1000),
        ];

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'description' => 'Test description',
                'size' => 'MD',
                'gender' => 'male',
                'cost_price' => 100.00,
                'sale_price' => 150.00,
                'status' => 'active',
                'initial_quantity' => 10,
                'images' => $images,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_update_product(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $brand = \App\Models\Brand::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Updated Product',
                'description' => 'Updated description',
                'size' => 'LG',
                'gender' => 'female',
                'cost_price' => 120.00,
                'sale_price' => 180.00,
                'status' => 'active',
                'images' => [],
                'primary_image_id' => $image->id,
                'remove_images' => [],
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function can_view_create_page(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.products.create'));
        $response->assertStatus(200);

        // The form should have the image preview container
        $response->assertSee('image-preview-container');
        $response->assertSee('image-preview-grid');
    }

    /** @test */
    public function can_view_edit_page_with_images(): void
    {
        $product = Product::factory()->create();

        ProductImage::factory()->count(3)->create([
            'product_id' => $product->id,
        ]);

        $this->actingAs($this->superAdmin);

        $response = $this->get(route('admin.products.edit', $product));
        $response->assertStatus(200);

        // The form should have the images grid
        $response->assertSee('images-grid');
        $response->assertSee('all-images-container');
    }

    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
