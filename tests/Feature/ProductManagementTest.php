<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Product Management Test Suite.
 *
 * Comprehensive tests for Product CRUD operations, authorization,
 * validation, slug generation, and security measures.
 */
class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageProductsPermission;
    protected Permission $viewProductsPermission;
    protected Permission $deleteProductsPermission;

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

        $this->deleteProductsPermission = Permission::firstOrCreate([
            'name' => 'delete products',
            'guard_name' => 'web',
        ]);

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo([$this->manageProductsPermission, $this->viewProductsPermission, $this->deleteProductsPermission]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([$this->manageProductsPermission, $this->viewProductsPermission, $this->deleteProductsPermission]);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    /** @test */
    public function super_admin_can_create_product(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Classic Cotton T-Shirt',
                'description' => 'A comfortable cotton t-shirt',
                'size' => 'L',
                'gender' => 'unisex',
                'cost_price' => '15.00',
                'sale_price' => '25.00',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Classic Cotton T-Shirt',
            'slug' => 'classic-cotton-t-shirt',
            'size' => 'L',
            'gender' => 'unisex',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_product(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'M',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function slug_is_auto_generated_from_name(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Summer Dress Collection',
                'size' => 'M',
                'gender' => 'female',
                'cost_price' => '20.00',
                'sale_price' => '40.00',
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('products', [
            'slug' => 'summer-dress-collection',
        ]);
    }

    /** @test */
    public function product_name_is_required(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => '',
                'size' => 'L',
                'gender' => 'unisex',
                'cost_price' => '15.00',
                'sale_price' => '25.00',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function product_size_must_be_valid(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'XXXL',
                'gender' => 'male',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['size']);
    }

    /** @test */
    public function product_gender_must_be_valid(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'L',
                'gender' => 'other',
                'cost_price' => '10.00',
                'sale_price' => '20.00',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['gender']);
    }

    /** @test */
    public function sale_price_must_be_greater_than_cost_price(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'L',
                'gender' => 'unisex',
                'cost_price' => '25.00',
                'sale_price' => '15.00',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['sale_price']);
    }

    /** @test */
    public function offer_price_must_be_less_than_sale_price(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.products.store'), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'Test Product',
                'size' => 'L',
                'gender' => 'unisex',
                'cost_price' => '15.00',
                'sale_price' => '25.00',
                'offer_price' => '30.00',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['offer_price']);
    }

    /** @test */
    public function can_update_product(): void
    {
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Old Product Name',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.products.update', $product), [
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'name' => 'New Product Name',
                'size' => 'XL',
                'gender' => 'female',
                'cost_price' => '20.00',
                'sale_price' => '35.00',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Product Name',
            'slug' => 'new-product-name',
        ]);
    }

    /** @test */
    public function can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.products.destroy', $product));

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_products_list(): void
    {
        Product::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewProductsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_products_list(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.products.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_filter_products_by_brand(): void
    {
        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();
        $category = Category::factory()->create();

        Product::factory()->create(['brand_id' => $brand1->id, 'category_id' => $category->id]);
        Product::factory()->create(['brand_id' => $brand2->id, 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['brand' => $brand1->id]));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertEquals(1, $products->count());
        $this->assertEquals($brand1->id, $products->first()->brand_id);
    }

    /** @test */
    public function can_filter_products_by_size(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        Product::factory()->create(['size' => 'S', 'brand_id' => $brand->id, 'category_id' => $category->id]);
        Product::factory()->create(['size' => 'L', 'brand_id' => $brand->id, 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['size' => 'S']));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertEquals(1, $products->count());
        $this->assertEquals('S', $products->first()->size);
    }

    /** @test */
    public function can_filter_products_by_gender(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        Product::factory()->create(['gender' => 'male', 'brand_id' => $brand->id, 'category_id' => $category->id]);
        Product::factory()->create(['gender' => 'female', 'brand_id' => $brand->id, 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['gender' => 'male']));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertEquals(1, $products->count());
        $this->assertEquals('male', $products->first()->gender);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->get(route('admin.products.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_view_product_details(): void
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewProductsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.products.show', $product));

        $response->assertStatus(200);
        $response->assertViewHas('product');
        $response->assertSee('Test Product');
    }

    protected function tearDown(): void
    {
        Cache::forget('spatie.permission.cache');
        parent::tearDown();
    }
}
