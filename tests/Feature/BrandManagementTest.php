<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Brand Management Test Suite.
 *
 * Comprehensive tests for Brand CRUD operations, authorization,
 * validation, logo upload, slug generation, and security measures.
 */
class BrandManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;
    protected Permission $manageBrandsPermission;
    protected Permission $viewBrandsPermission;
    protected Permission $deleteBrandsPermission;

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
        $this->manageBrandsPermission = Permission::firstOrCreate([
            'name' => 'manage brands',
            'guard_name' => 'web',
        ]);

        $this->viewBrandsPermission = Permission::firstOrCreate([
            'name' => 'view brands',
            'guard_name' => 'web',
        ]);

        $this->deleteBrandsPermission = Permission::firstOrCreate([
            'name' => 'delete brands',
            'guard_name' => 'web',
        ]);

        // Create super_admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->givePermissionTo([$this->manageBrandsPermission, $this->viewBrandsPermission, $this->deleteBrandsPermission]);

        // Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo([$this->manageBrandsPermission, $this->viewBrandsPermission, $this->deleteBrandsPermission]);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();

        // Configure fake storage for logo uploads
        Storage::fake('public');
    }

    /** @test */
    public function super_admin_can_create_brand(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Nike',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('brands', [
            'name' => 'Nike',
            'slug' => 'nike',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_with_permission_can_create_brand(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), [
                'name' => 'Adidas',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('brands', [
            'name' => 'Adidas',
            'slug' => 'adidas',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_brand(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'status' => 'active',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('brands', [
            'name' => 'Test Brand',
        ]);
    }

    /** @test */
    public function slug_is_auto_generated_from_name(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), [
                'name' => 'Puma Sport',
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('brands', [
            'name' => 'Puma Sport',
            'slug' => 'puma-sport',
        ]);
    }

    /** @test */
    public function duplicate_brand_name_is_rejected(): void
    {
        Brand::factory()->create(['name' => 'Nike']);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Nike',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function brand_name_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => '',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function brand_status_is_required(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'status' => '',
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function brand_status_must_be_valid(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'status' => 'invalid',
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function can_create_inactive_brand(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Inactive Brand',
                'status' => 'inactive',
            ]);

        $this->assertDatabaseHas('brands', [
            'name' => 'Inactive Brand',
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function can_update_brand(): void
    {
        $brand = Brand::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.brands.update', $brand), [
                'name' => 'New Name',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_brand(): void
    {
        $brand = Brand::factory()->create(['name' => 'Test Brand']);

        $response = $this->actingAs($this->regularUser)
            ->put(route('admin.brands.update', $brand), [
                'name' => 'Updated Name',
                'status' => 'active',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Test Brand',
        ]);
    }

    /** @test */
    public function can_delete_brand(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHasNoErrors();

        $this->assertSoftDeleted('brands', [
            'id' => $brand->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_brand(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertStatus(403);

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
        ]);
    }

    /** @test */
    public function user_with_view_permission_can_view_brands_list(): void
    {
        Brand::factory()->count(5)->create();

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewBrandsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.brands.index'));

        $response->assertStatus(200);
        $response->assertViewHas('brands');
    }

    /** @test */
    public function user_without_view_permission_cannot_view_brands_list(): void
    {
        Brand::factory()->count(5)->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.brands.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_view_brand_details(): void
    {
        $brand = Brand::factory()->create(['name' => 'Test Brand']);

        $user = User::factory()->create();
        $user->givePermissionTo($this->viewBrandsPermission);

        $response = $this->actingAs($user)
            ->get(route('admin.brands.show', $brand));

        $response->assertStatus(200);
        $response->assertViewHas('brand');
        $response->assertSee('Test Brand');
    }

    /** @test */
    public function can_search_brands_by_name(): void
    {
        Brand::factory()->create(['name' => 'Nike']);
        Brand::factory()->create(['name' => 'Adidas']);
        Brand::factory()->create(['name' => 'Puma']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.index', ['search' => 'Nike']));

        $response->assertStatus(200);
        $response->assertViewHas('brands');
        $this->assertEquals(1, $response->viewData('brands')->count());
    }

    /** @test */
    public function can_filter_brands_by_status(): void
    {
        Brand::factory()->create(['name' => 'Active Brand', 'status' => 'active']);
        Brand::factory()->create(['name' => 'Inactive Brand', 'status' => 'inactive']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.index', ['status' => 'active']));

        $response->assertStatus(200);
        $brands = $response->viewData('brands');
        $this->assertEquals(1, $brands->count());
        $this->assertEquals('Active Brand', $brands->first()->name);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_brands(): void
    {
        $response = $this->get(route('admin.brands.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function logo_upload_works(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('logo.jpg', 400, 400)
            ->size(1000);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'logo' => $file,
                'status' => 'active',
            ]);

        $brand = Brand::where('name', 'Test Brand')->first();
        $this->assertNotNull($brand->logo);
        Storage::disk('public')->assertExists($brand->logo);
    }

    /** @test */
    public function logo_upload_validates_file_type(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->create('document.pdf', 1000);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'logo' => $file,
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['logo']);
    }

    /** @test */
    public function logo_upload_validates_file_size(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('large-logo.jpg', 400, 400)
            ->size(5000); // 5MB, exceeds 2MB limit

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'logo' => $file,
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['logo']);
    }

    /** @test */
    public function logo_is_deleted_when_brand_is_deleted(): void
    {
        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()
            ->image('logo.jpg', 400, 400)
            ->size(1000);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.brands.store'), [
                'name' => 'Test Brand',
                'logo' => $file,
                'status' => 'active',
            ]);

        $brand = Brand::where('name', 'Test Brand')->first();
        $logoPath = $brand->logo;

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.brands.destroy', $brand));

        Storage::disk('public')->assertMissing($logoPath);
    }

    /** @test */
    public function can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.brands.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_edit_form(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.edit', $brand));

        $response->assertStatus(200);
        $response->assertViewHas('brand');
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
