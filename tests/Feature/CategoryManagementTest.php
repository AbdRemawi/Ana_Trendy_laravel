<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Category Management Test Suite.
 *
 * Comprehensive tests for Category CRUD operations, authorization,
 * validation, image upload, slug generation, hierarchical structure,
 * and security measures.
 */
class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageCategoriesPermission;
    protected Permission $viewCategoriesPermission;

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
        $this->manageCategoriesPermission = Permission::firstOrCreate([
            'name' => 'manage categories',
            'guard_name' => 'web',
        ]);

        $this->viewCategoriesPermission = Permission::firstOrCreate([
            'name' => 'view categories',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageCategoriesPermission, $this->viewCategoriesPermission]);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo([$this->manageCategoriesPermission, $this->viewCategoriesPermission]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();

        // Configure fake storage for image uploads
        Storage::fake('public');
    }

    /** @test */
    public function super_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Electronics',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'status' => 'active',
            'sort_order' => 1,
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Clothing',
                'status' => 'active',
                'sort_order' => 2,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_category(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
        ]);
    }

    /** @test */
    public function can_create_child_category(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Laptops',
                'parent_id' => $parent->id,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Laptops',
            'parent_id' => $parent->id,
        ]);
    }

    /** @test */
    public function slug_is_auto_generated_from_name(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Smart Phones & Tablets',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Smart Phones & Tablets',
            'slug' => 'smart-phones-tablets',
        ]);
    }

    /** @test */
    public function category_name_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => '',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function category_status_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'status' => '',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function category_status_must_be_valid(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'status' => 'invalid',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function category_sort_order_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'status' => 'active',
                'sort_order' => '',
            ]);

        $response->assertSessionHasErrors(['sort_order']);
    }

    /** @test */
    public function category_sort_order_must_be_numeric(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'status' => 'active',
                'sort_order' => 'abc',
            ]);

        $response->assertSessionHasErrors(['sort_order']);
    }

    /** @test */
    public function parent_id_must_exist_or_be_null(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'parent_id' => 999,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    /** @test */
    public function can_create_inactive_category(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Inactive Category',
                'status' => 'inactive',
                'sort_order' => 1,
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Inactive Category',
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'New Name',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
    }

    /** @test */
    public function can_update_category_parent(): void
    {
        $parent = Category::factory()->create(['name' => 'Parent']);
        $category = Category::factory()->create(['name' => 'Child']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Child',
                'parent_id' => $parent->id,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $parent->id,
        ]);
    }

    /** @test */
    public function category_cannot_be_own_parent(): void
    {
        $category = Category::factory()->create(['name' => 'Test']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Test',
                'parent_id' => $category->id,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['parent_id']);
    }

    /** @test */
    public function regular_user_cannot_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Name',
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Test Category',
        ]);
    }

    /** @test */
    public function can_delete_category_without_children(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function cannot_delete_category_with_children(): void
    {
        $parent = Category::factory()->create(['name' => 'Parent']);
        Category::factory()->create(['name' => 'Child', 'parent_id' => $parent->id]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.categories.destroy', $parent));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $parent->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertStatus(403);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_categories_list(): void
    {
        Category::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCategoriesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('nestedCategories');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_categories_list(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.categories.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewHas('parentCategories');
    }

    /** @test */
    public function regular_user_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.categories.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_edit_form(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewHas('category');
        $response->assertViewHas('parentCategories');
    }

    /** @test */
    public function regular_user_cannot_access_edit_form(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_categories(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function image_upload_works(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('category.jpg', 800, 600)
            ->size(1000);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'image' => $file,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $category = Category::where('name', 'Test Category')->first();
        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    /** @test */
    public function image_upload_validates_file_type(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->create('document.pdf', 1000);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'image' => $file,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function image_upload_validates_file_size(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('large-image.jpg', 800, 600)
            ->size(5000); // 5MB, exceeds 2MB limit

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'image' => $file,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function image_is_deleted_when_category_is_deleted(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('category.jpg', 800, 600)
            ->size(1000);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'image' => $file,
                'status' => 'active',
                'sort_order' => 1,
            ]);

        $category = Category::where('name', 'Test Category')->first();
        $imagePath = $category->image;

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.categories.destroy', $category));

        Storage::disk('public')->assertMissing($imagePath);
    }

    /** @test */
    public function can_restore_soft_deleted_category(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.restore', $category->id));

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function hierarchical_categories_render_correctly(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'Laptops', 'parent_id' => $parent->id, 'sort_order' => 1]);
        Category::factory()->create(['name' => 'Phones', 'parent_id' => $parent->id, 'sort_order' => 2]);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewCategoriesPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Electronics');
        $response->assertSee('Laptops');
        $response->assertSee('Phones');
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
