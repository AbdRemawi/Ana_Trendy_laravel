<?php

return [
    // Page Titles
    'roles_management' => 'Roles Management',
    'permissions_management' => 'Permissions Management',
    'users_management' => 'Users Management',
    'brands_management' => 'Brands Management',
    'roles' => 'Roles',
    'permissions' => 'Permissions',
    'users' => 'Users',
    'brands' => 'Brands',

    // Descriptions
    'roles_description' => 'Manage user roles and their permissions',
    'permissions_description' => 'Manage system permissions',
    'users_description' => 'Manage system users and their accounts',
    'brands_description' => 'Manage product brands',

    // CRUD Actions
    'create_role' => 'Create Role',
    'edit_role' => 'Edit Role',
    'delete_role' => 'Delete Role',
    'update_role' => 'Update Role',
    'sync_permissions' => 'Sync Permissions',

    'create_permission' => 'Create Permission',
    'edit_permission' => 'Edit Permission',
    'delete_permission' => 'Delete Permission',
    'update_permission' => 'Update Permission',

    'create_user' => 'Create User',
    'edit_user' => 'Edit User',
    'delete_user' => 'Delete User',
    'update_user' => 'Update User',

    'create_brand' => 'Create Brand',
    'edit_brand' => 'Edit Brand',
    'delete_brand' => 'Delete Brand',
    'update_brand' => 'Update Brand',

    // Common Labels
    'actions' => 'Actions',
    'view' => 'View',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'back' => 'Back',

    // Role Labels
    'role_name' => 'Role Name',
    'role_name_placeholder' => 'e.g. content_manager',
    'role_details' => 'Role Details',
    'permissions_count' => 'Permissions',
    'assigned' => 'assigned',
    'no_permissions' => 'No permissions',
    'no_permissions_assigned' => 'No permissions assigned to this role',

    // Permission Labels
    'permission_name' => 'Permission Name',
    'permission_name_help' => 'Use lowercase, spaces, or underscores (e.g. "manage products")',
    'permission_details' => 'Permission Details',
    'roles_count' => 'Used By',
    'role' => 'role',
    'roles' => 'roles',
    'not_used' => 'Not used',
    'not_assigned_to_any_role' => 'Not assigned to any role',

    // Dates
    'created_at' => 'Created',
    'updated_at' => 'Updated',

    // Empty States
    'no_roles_found' => 'No roles found',
    'no_roles_description' => 'Get started by creating your first role',
    'create_first_role' => 'Create First Role',

    'no_permissions_found' => 'No permissions found',
    'no_permissions_description' => 'Get started by creating your first permission',
    'create_first_permission' => 'Create First Permission',

    // Confirmations
    'confirm_delete_role' => 'Are you sure you want to delete this role',
    'confirm_delete_permission' => 'Are you sure you want to delete this permission',

    // Role Edit/Create
    'create_role_description' => 'Create a new role and assign permissions',
    'edit_role_description' => 'Update role name and permissions',
    'permissions_after_creation' => 'Permissions Assigned After Creation',
    'permissions_after_creation_description' => 'Create the role first, then you can assign permissions to it.',
    'next_step' => 'Next Step',
    'after_role_created' => 'After creating the role, you will be able to assign permissions to it.',
    'toggle_permissions_help' => 'Toggle permissions to grant or revoke access',
    'information' => 'Information',
    'dashboard' => 'Dashboard',
    'users' => 'Users',
    'products' => 'Products',
    'orders' => 'Orders',
    'affiliate' => 'Affiliate',
    'system' => 'System',

    // Permission Edit/Create
    'create_permission_description' => 'Create a new system permission',
    'auto_format' => 'Auto-format',
    'permission_auto_format_description' => 'Permission names will be automatically converted to snake_case format.',
    'in_use' => 'In Use',
    'permission_in_use_description' => 'This permission is assigned to :count role(s). Changing it will affect all assigned roles.',
    'roles_using_permission' => 'Roles Using This Permission',
    'assigned_roles' => 'Assigned Roles',

    // Special Roles
    'super_admin' => 'Super Admin',

    // Warnings
    'cannot_delete_used_permission' => 'Cannot delete permission that is in use',
    'cannot_delete_super_admin' => 'Cannot delete super_admin role',

    // Pagination
    'showing' => 'Showing :from to :to of :total results',

    // ============ USER MANAGEMENT ============
    'user_name' => 'Name',
    'user_mobile' => 'Mobile Number',
    'user_email' => 'Email',
    'user_password' => 'Password',
    'user_role' => 'Role',
    'user_status' => 'Status',
    'user_details' => 'User Details',

    'mobile_placeholder' => 'e.g. 0791234567',
    'mobile_help' => 'Must be 10 digits starting with 078, 079, or 077',

    'password_required' => 'Password is required',
    'password_optional' => 'Leave empty to keep current password',
    'password_placeholder' => 'Enter at least 8 characters',

    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'status_suspended' => 'Suspended',

    'create_user_description' => 'Create a new user account and assign role',
    'edit_user_description' => 'Update user details and role assignment',

    // User Success Messages
    'user_created_successfully' => 'User created successfully.',
    'user_updated_successfully' => 'User updated successfully.',
    'user_deleted_successfully' => 'User ":name" deleted successfully.',

    // User Error Messages
    'cannot_delete_super_admin_user' => 'Cannot delete super admin users.',
    'cannot_delete_own_account' => 'Cannot delete your own account.',
    'cannot_modify_own_role' => 'Cannot modify your own role.',
    'cannot_assign_super_admin' => 'Only super admins can assign the super admin role.',

    // Role Success Messages
    'role_created' => 'Role ":name" created successfully.',
    'role_updated' => 'Role updated successfully.',
    'role_deleted' => 'Role ":name" deleted successfully.',
    'role_deleted_with_users' => 'Role ":name" deleted successfully. Removed from :count user(s).',
    'cannot_delete_role_with_users' => 'Cannot delete role assigned to :count user(s). Please reassign users first.',

    // Permission Success Messages
    'permission_created' => 'Permission ":name" created successfully.',
    'permission_updated' => 'Permission updated successfully.',
    'permission_deleted' => 'Permission ":name" deleted successfully.',
    'cannot_delete_permission_in_use' => 'Cannot delete permission assigned to roles: :roles.',

    // User Empty States
    'no_users_found' => 'No users found',
    'no_users_description' => 'Get started by creating your first user',
    'create_first_user' => 'Create First User',

    'confirm_delete_user' => 'Are you sure you want to delete this user',

    // Search & Filter
    'search_users' => 'Search by name, email, or mobile',
    'filter_by_role' => 'Filter by Role',
    'all_roles' => 'All Roles',

    // ============ BRAND MANAGEMENT ============
    'brand_name' => 'Brand Name',
    'brand_slug' => 'Slug',
    'brand_logo' => 'Logo',
    'brand_status' => 'Status',
    'brand_details' => 'Brand Details',
    'brands_list' => 'Brands List',

    'brand_name_placeholder' => 'e.g. Nike',
    'brand_logo_help' => 'Upload brand logo (JPEG, PNG, or WEBP, max 2MB)',
        'create_brand_description' => 'Create a new brand',
        'edit_brand_description' => 'Update brand details',

    'create_brand_description' => 'Create a new brand',
    'edit_brand_description' => 'Update brand details',

    // Brand Success Messages
    'brand_created_successfully' => 'Brand created successfully.',
    'brand_updated_successfully' => 'Brand updated successfully.',
    'brand_deleted_successfully' => 'Brand ":name" deleted successfully.',
    'brand_restored_successfully' => 'Brand restored successfully.',

    // Brand Empty States
    'no_brands_found' => 'No brands found',
    'no_brands_description' => 'Get started by creating your first brand',
    'create_first_brand' => 'Create First Brand',

    'confirm_delete_brand' => 'Are you sure you want to delete this brand',

    // Search & Filter Brands
    'search_brands' => 'Search by brand name',
    'filter_by_status' => 'Filter by Status',
    'all_statuses' => 'All Statuses',
    'search' => 'Search',
    'clear' => 'Clear',

    // ============ CATEGORY MANAGEMENT ============
    'categories_management' => 'Categories Management',
    'categories' => 'Categories',
    'categories_description' => 'Manage hierarchical product categories',

    'category_name' => 'Category Name',
    'category_slug' => 'Slug',
    'category_parent' => 'Parent Category',
    'category_image' => 'Image',
    'category_status' => 'Status',
    'category_sort_order' => 'Sort Order',
    'category_details' => 'Category Details',
    'categories_list' => 'Categories List',

    'category_name_placeholder' => 'e.g. Electronics',
    'category_image_help' => 'Upload category image (JPEG, PNG, or WEBP, max 2MB)',

    'create_category' => 'Create Category',
    'edit_category' => 'Edit Category',
    'delete_category' => 'Delete Category',
    'update_category' => 'Update Category',

    'create_category_description' => 'Create a new category',
    'edit_category_description' => 'Update category details',

    'no_parent' => 'No Parent',
    'select_parent_category' => 'Select Parent Category (Optional)',

    // Category Success Messages
    'category_created_successfully' => 'Category created successfully.',
    'category_updated_successfully' => 'Category updated successfully.',
    'category_deleted_successfully' => 'Category deleted successfully.',
    'category_restored_successfully' => 'Category restored successfully.',
    'category_has_children' => 'Cannot delete category with sub-categories',

    // Category Empty States
    'no_categories_found' => 'No categories found',
    'no_categories_description' => 'Get started by creating your first category',
    'create_first_category' => 'Create First Category',

    'confirm_delete_category' => 'Are you sure you want to delete this category',

    // Search & Filter Categories
    'search_categories' => 'Search by category name',

    // ============ PRODUCT MANAGEMENT ============
    'products_management' => 'Products Management',
    'products' => 'Products',
    'products_description' => 'Manage product catalog with images and inventory',

    'product_name' => 'Product Name',
    'product_slug' => 'Slug',
    'product_brand' => 'Brand',
    'product_category' => 'Category',
    'product_description' => 'Description',
    'product_size' => 'Size',
    'product_gender' => 'Gender',
    'product_cost_price' => 'Cost Price',
    'product_sale_price' => 'Sale Price',
    'product_offer_price' => 'Offer Price',
    'product_status' => 'Status',
    'product_stock' => 'Stock',
    'product_details' => 'Product Details',
    'products_list' => 'Products List',

    'product_name_placeholder' => 'e.g. Classic Cotton T-Shirt',
    'product_description_placeholder' => 'Enter product description...',
    'product_size_help' => 'Select the size: S, M, L, XL, or XXL',
    'product_gender_help' => 'Select the target gender: male, female, or unisex',

    // Product Sizes
    'size_s' => 'S',
    'size_m' => 'M',
    'size_l' => 'L',
    'size_xl' => 'XL',
    'size_xxl' => 'XXL',
    'all_sizes' => 'All Sizes',

    // Product Genders
    'gender_male' => 'Male',
    'gender_female' => 'Female',
    'gender_unisex' => 'Unisex',
    'all_genders' => 'All Genders',

    'create_product' => 'Create Product',
    'edit_product' => 'Edit Product',
    'delete_product' => 'Delete Product',
    'update_product' => 'Update Product',

    'create_product_description' => 'Create a new product',
    'edit_product_description' => 'Update product details',

    // Product Success Messages
    'product_created_successfully' => 'Product created successfully.',
    'product_updated_successfully' => 'Product updated successfully.',
    'product_deleted_successfully' => 'Product ":name" deleted successfully.',
    'product_restored_successfully' => 'Product restored successfully.',

    // Product Empty States
    'no_products_found' => 'No products found',
    'no_products_description' => 'Get started by creating your first product',
    'create_first_product' => 'Create First Product',

    'confirm_delete_product' => 'Are you sure you want to delete this product',

    // Search & Filter Products
    'search_products' => 'Search by product name',
    'filter_by_brand' => 'Filter by Brand',
    'filter_by_category' => 'Filter by Category',
    'filter_by_size' => 'Filter by Size',
    'filter_by_gender' => 'Filter by Gender',
    'all_brands' => 'All Brands',
    'all_categories' => 'All Categories',

    // ============ PRODUCT IMAGES ============
    'product_images' => 'Product Images',
    'product_image' => 'Product Image',
    'product_images_management' => 'Product Images Management',
    'product_images_description' => 'Manage product images gallery',

    'image_file' => 'Image File',
    'is_primary' => 'Primary Image',
    'sort_order' => 'Sort Order',
    'image_details' => 'Image Details',

    'set_primary_image' => 'Set as Primary',
    'upload_image' => 'Upload Image',
    'upload_images' => 'Upload Images',
    'manage_images' => 'Manage Images',
    'image_gallery' => 'Image Gallery',

    'product_image_help' => 'Upload product image (JPEG, PNG, or WEBP, max 2MB)',

    // Product Image Success Messages
    'product_image_created_successfully' => 'Product image created successfully.',
    'product_image_updated_successfully' => 'Product image updated successfully.',
    'product_image_deleted_successfully' => 'Product image deleted successfully.',
    'product_image_set_primary_successfully' => 'Primary image updated successfully.',
    'product_images_reordered_successfully' => 'Images reordered successfully.',

    'confirm_delete_product_image' => 'Are you sure you want to delete this image',
    'no_images_found' => 'No images found',
    'no_images_description' => 'Upload images for this product',

    // ============ INVENTORY MANAGEMENT ============
    'inventory_management' => 'Inventory Management',
    'inventory' => 'Inventory',
    'inventory_description' => 'Track stock movements and transactions',

    'transaction_type' => 'Transaction Type',
    'transaction_quantity' => 'Quantity',
    'transaction_notes' => 'Notes',
    'transaction_date' => 'Date',

    // Transaction Types
    'type_supply' => 'Supply',
    'type_sale' => 'Sale',
    'type_return' => 'Return',
    'type_damage' => 'Damage',
    'type_adjustment' => 'Adjustment',
    'all_types' => 'All Types',

    'create_transaction' => 'Create Transaction',
    'edit_transaction' => 'Edit Transaction',
    'delete_transaction' => 'Delete Transaction',

    'create_transaction_description' => 'Record a stock movement',
    'transaction_type_help' => 'Select the type of stock movement',
    'transaction_quantity_help' => 'Enter the quantity (positive number)',
    'transaction_notes_help' => 'Add optional notes for reference',

    // Inventory Success Messages
    'inventory_transaction_created_successfully' => 'Inventory transaction created successfully.',
    'inventory_transaction_updated_successfully' => 'Inventory transaction updated successfully.',
    'inventory_transaction_deleted_successfully' => 'Inventory transaction deleted successfully.',

    'confirm_delete_inventory_transaction' => 'Are you sure you want to delete this transaction',
    'no_transactions_found' => 'No transactions found',
    'product_inventory' => 'Product Inventory',
    'view_inventory' => 'View Inventory',
    'current_stock' => 'Current Stock',

    // ============ PRICES & DISCOUNTS ============
    'price' => 'Price',
    'prices' => 'Prices',
    'cost_price_help' => 'The cost price for purchasing/producing the product',
    'sale_price_help' => 'The regular selling price (must be greater than cost price)',
    'offer_price_help' => 'Optional promotional price (must be less than sale price)',
    'profit_margin' => 'Profit Margin',
    'has_offer' => 'Has Offer',
    'effective_price' => 'Effective Price',

    // ============ MISSING KEYS ============
    'main_navigation' => 'Main Navigation',
    'users_table' => 'Users Table',
    'user_actions' => 'User Actions',
    'you' => 'You',
    'not_provided' => 'Not provided',
    'no_role' => 'No role',
    'toggle_permission' => 'Toggle permission: :permission',

    // ============ COMMON LABELS ============
    'optional' => 'Optional',
    'note' => 'Note',
    'attributes' => 'Attributes',
    'select_brand' => 'Select Brand',
    'select_category' => 'Select Category',
    'select_product' => 'Select Product',
    'select_size' => 'Select Size',
    'select_gender' => 'Select Gender',

    // ============ PRODUCT IMAGES ============
    'primary_image_notice' => 'The first image will be set as the primary image. You can change this on the edit page.',
    'max_10_images' => 'Max 10 images',
    'images_help' => 'Allowed formats: JPEG, PNG, WebP. Max 2MB per image.',
    'image_preview' => 'Image Preview',
    'no_images_uploaded' => 'No images uploaded yet',
    'primary' => 'PRIMARY',
    'select_primary_image_help' => 'Click on an image to set it as the primary image.',
    'upload_new_images' => 'Upload New Images',
    'remove_image' => 'Remove Image',
    'confirm_remove_image' => 'Are you sure you want to remove this image?',
    'new_images_will_add' => 'New images will be uploaded and added when you save. You can then select the primary image.',
    'at_least_one_image_required' => 'At least one image is required.',
    'select_primary_image' => 'Please select a primary image.',
    'initial_supply_transaction' => 'Initial stock on product creation',

    // ============ PRODUCT HELP TEXT ============
    'product_name_help' => 'The product name will be used to automatically generate a unique slug.',

    // ============ INITIAL INVENTORY ============
    'initial_inventory' => 'Initial Inventory',
    'initial_inventory_help' => 'Set the starting stock quantity for this product.',
    'initial_stock_quantity' => 'Initial Stock Quantity',
    'initial_stock_help' => 'This quantity will be recorded as an initial supply transaction.',
    'initial_stock_note' => 'Stock can be modified later through inventory management.',

    // ============ INVENTORY MESSAGES ============
    'no_transactions_description' => 'No inventory transactions recorded yet',

    // Transaction Type Labels
    'transaction_types' => 'Transaction Types',
    'select_type' => 'Select Type',
    'supply_desc' => 'Add stock to inventory',
    'sale_desc' => 'Record a sale (removes stock)',
    'return_desc' => 'Customer return (adds stock)',
    'damage_desc' => 'Damaged goods (removes stock)',
    'adjustment_desc' => 'Manual stock correction',

    // Inventory Validation Messages
    'quantity_positive_for_type' => 'The quantity must be a positive number for :type transactions.',
    'inventory_would_go_negative' => 'This transaction would result in negative stock (current: :current_stock, projected: :projected_stock, requested: :requested_quantity).',
    'adjustment_would_go_negative' => 'This adjustment would result in negative stock (current: :current_stock, adjustment: :adjustment_value).',

    // ============ VALIDATION MESSAGES ============
    // Brand
    'validation_brand_required' => 'The brand field is required.',
    'validation_brand_exists' => 'The selected brand is invalid.',

    // Category
    'validation_category_required' => 'The category field is required.',
    'validation_category_exists' => 'The selected category is invalid.',

    // Product Name
    'validation_name_required' => 'The product name field is required.',
    'validation_name_max' => 'The product name may not be greater than 255 characters.',

    // Description
    'validation_description_max' => 'The description may not be greater than 5000 characters.',

    // Size
    'validation_size_required' => 'The size field is required.',
    'validation_size_in' => 'The selected size is invalid.',

    // Gender
    'validation_gender_required' => 'The gender field is required.',
    'validation_gender_in' => 'The selected gender is invalid.',

    // Cost Price
    'validation_cost_price_required' => 'The cost price field is required.',
    'validation_cost_price_decimal' => 'The cost price must be a valid decimal number.',
    'validation_cost_price_min' => 'The cost price must be at least 0.',
    'validation_cost_price_max' => 'The cost price may not be greater than 99999999.99.',

    // Sale Price
    'validation_sale_price_required' => 'The sale price field is required.',
    'validation_sale_price_decimal' => 'The sale price must be a valid decimal number.',
    'validation_sale_price_min' => 'The sale price must be at least 0.',
    'validation_sale_price_max' => 'The sale price may not be greater than 99999999.99.',
    'validation_sale_price_gt' => 'The sale price must be greater than the cost price.',

    // Offer Price
    'validation_offer_price_decimal' => 'The offer price must be a valid decimal number.',
    'validation_offer_price_min' => 'The offer price must be at least 0.',
    'validation_offer_price_max' => 'The offer price may not be greater than 99999999.99.',
    'validation_offer_price_lt' => 'The offer price must be less than the sale price.',

    // Status
    'validation_status_required' => 'The status field is required.',
    'validation_status_in' => 'The selected status is invalid.',

    // Initial Quantity
    'validation_initial_quantity_required' => 'The initial stock quantity field is required.',
    'validation_initial_quantity_integer' => 'The initial stock quantity must be an integer.',
    'validation_initial_quantity_min' => 'The initial stock quantity must be at least 0.',

    // Images
    'validation_images_required' => 'At least one product image is required.',
    'validation_images_array' => 'Images must be an array.',
    'validation_images_min' => 'At least one product image is required.',
    'validation_images_max' => 'Cannot upload more than 10 images.',
    'validation_images_required_file' => 'An image file is required.',
    'validation_images_image' => 'The uploaded file must be an image.',
    'validation_images_mimes' => 'Images must be files of type: jpeg, png, jpg, webp.',
    'validation_images_max_file' => 'Each image may not be greater than 2MB.',

    // Primary Image ID
    'validation_primary_image_id_integer' => 'Primary image selection is invalid.',
    'validation_primary_image_id_exists' => 'The selected primary image does not exist.',
    'validation_primary_image_removed' => 'The selected primary image is being removed. Please select another primary image.',
    'validation_primary_image_invalid' => 'The selected primary image is invalid.',

    // Remove Images
    'validation_remove_images_integer' => 'Image ID to remove must be an integer.',
    'validation_remove_images_exists' => 'Image ID to remove does not exist.',

    // Product Inventory
    'validation_product_required' => 'The product field is required.',
    'validation_product_exists' => 'The selected product is invalid.',

    // Transaction Type
    'validation_type_required' => 'The transaction type field is required.',
    'validation_type_in' => 'The transaction type must be one of: supply, sale, return, damage, adjustment.',

    // Transaction Quantity
    'validation_quantity_required' => 'The quantity field is required.',
    'validation_quantity_integer' => 'The quantity must be an integer.',
    'validation_quantity_min' => 'The quantity may not be less than -999999.',
    'validation_quantity_max' => 'The quantity may not be greater than 999999.',

    // Notes
    'validation_notes_max' => 'The notes may not be greater than 1000 characters.',

    // ============ VALIDATION ATTRIBUTES ============
    "attribute_brand" => "Brand",
    "attribute_category" => "Category",
    "attribute_product_name" => "Product Name",
    "attribute_description" => "Description",
    "attribute_size" => "Size",
    "attribute_gender" => "Gender",
    "attribute_cost_price" => "Cost Price",
    "attribute_sale_price" => "Sale Price",
    "attribute_offer_price" => "Offer Price",
    "attribute_status" => "Status",
    "attribute_initial_stock_quantity" => "Initial Stock Quantity",
    "attribute_images" => "Product Images",
    "attribute_primary_image" => "Primary Image",
    "attribute_remove_images" => "Images to Remove",
    "attribute_product" => "Product",
    "attribute_transaction_type" => "Transaction Type",
    "attribute_quantity" => "Quantity",
    "attribute_notes" => "Notes",

    // ============ ROLE VALIDATION MESSAGES ============
    "validation_name_required" => "The role name is required.",
    "validation_name_min" => "The role name must be at least 2 characters.",
    "validation_name_max" => "The role name may not be greater than 50 characters.",
    "validation_name_regex" => "The role name must contain only lowercase letters, numbers, and underscores.",
    "validation_name_unique" => "A role with this name already exists.",
    "validation_permissions_array" => "Permissions must be an array of permission names.",
    "validation_permissions_exists" => "One or more selected permissions do not exist.",

    // ============ ROLE EDIT SPECIFIC ============
    "role_name_readonly" => "Role name cannot be changed after creation.",
    "dashboard_permission_required" => "Always required to access the dashboard.",

    // ============ DELETE ROLE MODAL ============
    "delete_role_title" => "Delete Role",
    "delete_role_warning" => "Are you sure you want to delete the ':role' role? This action cannot be undone.",
    "confirm_delete" => "Yes, delete role",
    "delete_role_confirmation" => "This role will be permanently deleted.",
    "irreversible_action" => "This action cannot be undone and will permanently delete the role.",
    "processing" => "Processing...",

    // ============ DELETE USER MODAL ============
    "delete_user_title" => "Delete User",
    "delete_user_warning" => "Are you sure you want to delete this user? This action cannot be undone.",
    "confirm_delete_user" => "Yes, delete user",
    "delete_user_confirmation" => "This user will be permanently deleted.",

    // ============ ADDITIONAL USER MANAGEMENT TRANSLATIONS ============
    "back_to_users" => "Back to users",
    "viewing_user_details" => "Viewing details for :name",
    "commission_rate" => "Commission Rate",
    "commission_rate_help" => "Percentage of commission for affiliate (0-100)",
    "role_assignment_help" => "Assign a role to determine user permissions.",
    "current_user_info" => "Current User Info",
    "warning" => "Warning",
    "important_note" => "Important Note",
    "super_admin_warning" => "Only super admins can assign the super admin role.",
    "select_role" => "Select Role",
    "permissions" => "permissions",
    "more_permissions" => "more",
    "no_direct_permissions" => "No direct permissions assigned",
    "role_permissions" => "Role Permissions",
    "leave_empty_keep_password" => "Leave empty to keep current password.",
    "user_information" => "User Information",
    "view_user" => "View User",
    "edit_user" => "Edit User",
    "delete_user_button" => "Delete User",
    "password_placeholder" => "Enter at least 8 characters",
    "password_optional" => "Optional",

    // ============ USER VALIDATION MESSAGES ============
    "validation_name_required" => "The name field is required.",
    "validation_name_max" => "The name may not be greater than 255 characters.",
    "mobile_unique" => "This mobile number is already registered.",
    "validation_email_email" => "The email must be a valid email address.",
    "email_unique" => "This email is already registered.",
    "password_min" => "The password must be at least 8 characters.",
    "validation_role_required" => "The role field is required.",
    "validation_role_exists" => "The selected role is invalid.",
    "validation_status_required" => "The status field is required.",
    "validation_status_in" => "The selected status is invalid.",
    "validation_commission_numeric" => "The commission rate must be a number.",
    "validation_commission_min" => "The commission rate must be at least 0.",
    "validation_commission_max" => "The commission rate may not be greater than 100.",

    // ============ CITY MANAGEMENT ============
    'cities_management' => 'Cities Management',
    'cities' => 'Cities',
    'cities_description' => 'Manage cities for delivery',
    'city_name' => 'City Name',
    'city_status' => 'City Status',
    'city_details' => 'City Details',
    'cities_list' => 'Cities List',

    'city_name_placeholder' => 'e.g. Amman',
    'create_city' => 'Create City',
    'edit_city' => 'Edit City',
    'delete_city' => 'Delete City',
    'update_city' => 'Update City',

    'create_city_description' => 'Create a new city',
    'edit_city_description' => 'Update city details',

    // City Success Messages
    'city_created_successfully' => 'City created successfully.',
    'city_updated_successfully' => 'City updated successfully.',
    'city_deleted_successfully' => 'City ":name" deleted successfully.',
    'city_restored_successfully' => 'City restored successfully.',
    'city_status_updated' => 'City status updated to ":status".',

    // City Empty States
    'no_cities_found' => 'No cities found',
    'no_cities_description' => 'Get started by creating your first city',
    'create_first_city' => 'Create First City',
    'back_to_cities' => 'Back to cities',
    'confirm_delete_city' => 'Are you sure you want to delete this city',

    // Search & Filter Cities
    'search_cities' => 'Search by city name',

    // City Validation Messages
    'validation_city_name_required' => 'The city name field is required.',
    'validation_city_name_max' => 'The city name may not be greater than 255 characters.',
    'validation_city_name_unique' => 'This city name already exists.',
    'validation_is_active_required' => 'The status field is required.',
    'validation_is_active_boolean' => 'The status must be true or false.',

    // ============ DELIVERY COURIER MANAGEMENT ============
    'delivery_couriers_management' => 'Delivery Couriers Management',
    'delivery_couriers' => 'Delivery Couriers',
    'delivery_couriers_description' => 'Manage delivery courier companies',

    'courier_name' => 'Courier Name',
    'courier_contact_phone' => 'Contact Phone',
    'courier_status' => 'Courier Status',
    'courier_details' => 'Courier Details',
    'couriers_list' => 'Couriers List',

    'courier_name_placeholder' => 'e.g. Aramex',
    'courier_contact_phone_placeholder' => 'e.g. +962791234567',
    'create_courier' => 'Create Courier',
    'edit_courier' => 'Edit Courier',
    'delete_courier' => 'Delete Courier',
    'update_courier' => 'Update Courier',

    'create_courier_description' => 'Create a new delivery courier',
    'edit_courier_description' => 'Update courier details',

    // Courier Success Messages
    'courier_created_successfully' => 'Courier created successfully.',
    'courier_updated_successfully' => 'Courier updated successfully.',
    'courier_deleted_successfully' => 'Courier ":name" deleted successfully.',
    'courier_restored_successfully' => 'Courier restored successfully.',
    'courier_status_updated' => 'Courier status updated to ":status".',

    // Courier Empty States
    'no_couriers_found' => 'No couriers found',
    'no_couriers_description' => 'Get started by creating your first courier',
    'create_first_courier' => 'Create First Courier',
    'back_to_couriers' => 'Back to couriers',
    'confirm_delete_courier' => 'Are you sure you want to delete this courier',

    // Search & Filter Couriers
    'search_couriers' => 'Search by courier name',

    // Courier Validation Messages
    'validation_courier_name_required' => 'The courier name field is required.',
    'validation_courier_name_max' => 'The courier name may not be greater than 255 characters.',
    'validation_courier_name_unique' => 'This courier name already exists.',
    'validation_contact_phone_max' => 'The contact phone may not be greater than 20 characters.',

    // ============ DELIVERY COURIER FEE MANAGEMENT ============
    'delivery_courier_fees_management' => 'Delivery Courier Fees Management',
    'delivery_courier_fees' => 'Delivery Courier Fees',
    'delivery_courier_fees_description' => 'Manage delivery pricing per courier per city',

    'real_fee_amount' => 'Real Fee Amount',
    'display_fee_amount' => 'Display Fee Amount',
    'currency' => 'Currency',
    'fee_status' => 'Fee Status',
    'fee_details' => 'Fee Details',
    'fee_information' => 'Fee Information',
    'fees_list' => 'Fees List',

    'create_fee' => 'Create Fee',
    'edit_fee' => 'Edit Fee',
    'delete_fee' => 'Delete Fee',
    'update_fee' => 'Update Fee',

    'create_fee_description' => 'Create a new delivery courier fee',
    'edit_fee_description' => 'Update fee details',
    'fee_details_description' => 'View and manage fee details',

    'profit_amount' => 'Profit Amount',
    'profit_margin' => 'Profit Margin',

    'important_note' => 'Important Note',
    'fee_unique_constraint_note' => 'Only ONE fee record is allowed per courier per city combination.',

    // Fee Success Messages
    'fee_created_successfully' => 'Fee created successfully.',
    'fee_updated_successfully' => 'Fee updated successfully.',
    'fee_deleted_successfully' => 'Fee ":info" deleted successfully.',
    'fee_status_updated' => 'Fee status updated to ":status".',

    // Fee Empty States
    'no_fees_found' => 'No fees found',
    'no_fees_description' => 'Get started by creating your first fee',
    'create_first_fee' => 'Create First Fee',
    'back_to_fees' => 'Back to fees',
    'confirm_delete_fee' => 'Are you sure you want to delete this fee',

    // Search & Filter Fees
    'search_fees' => 'Search by courier name or city name',
    'all_couriers' => 'All Couriers',
    'all_cities' => 'All Cities',

    // Fee Validation Messages
    'validation_courier_id_required' => 'The courier field is required.',
    'validation_courier_id_integer' => 'The courier must be an integer.',
    'validation_courier_id_exists' => 'The selected courier is invalid or inactive.',
    'validation_city_id_required' => 'The city field is required.',
    'validation_city_id_integer' => 'The city must be an integer.',
    'validation_city_id_exists' => 'The selected city is invalid or inactive.',
    'validation_real_fee_amount_required' => 'The real fee amount field is required.',
    'validation_real_fee_amount_numeric' => 'The real fee amount must be a number.',
    'validation_real_fee_amount_decimal' => 'The real fee amount must have up to 3 decimal places.',
    'validation_real_fee_amount_min' => 'The real fee amount must be at least 0.',
    'validation_real_fee_amount_max' => 'The real fee amount may not be greater than 99999999.999.',
    'validation_display_fee_amount_required' => 'The display fee amount field is required.',
    'validation_display_fee_amount_numeric' => 'The display fee amount must be a number.',
    'validation_display_fee_amount_decimal' => 'The display fee amount must have up to 3 decimal places.',
    'validation_display_fee_amount_min' => 'The display fee amount must be at least 0.',
    'validation_display_fee_amount_max' => 'The display fee amount may not be greater than 99999999.999.',
    'validation_currency_required' => 'The currency field is required.',
    'validation_currency_max' => 'The currency may not be greater than 3 characters.',
    'validation_courier_city_combination_unique' => 'A fee for this courier and city combination already exists.',

    // Common Labels for Courier Fees
    'select_courier' => 'Select Courier',
    'select_city' => 'Select City',

    // ============ DELIVERY FEES RELATIONSHIP ============
    'delivery_fees_for_city' => 'Delivery Fees for City',
    'delivery_fees_for_courier' => 'Delivery Fees for Courier',
    'no_delivery_fees_found' => 'No delivery fees found',
    'no_delivery_fees_description' => 'No fees have been configured yet.',

    // ============ ORDER MANAGEMENT ============
    'orders_management' => 'Orders Management',
    'orders_description' => 'Manage customer orders and delivery',
    'order_number' => 'Order Number',
    'customer_name' => 'Customer Name',
    'customer_address' => 'Delivery Address',
    'order_city' => 'City',
    'order_courier' => 'Delivery Courier',
    'order_status' => 'Status',
    'order_items' => 'Order Items',
    'order_details' => 'Order Details',
    'orders_list' => 'Orders List',
    'order_notes' => 'Notes',
    'order_phone' => 'Phone Number',
    'order_phone_numbers' => 'Phone Numbers',
    'subtotal_products' => 'Products Subtotal',
    'delivery_fee' => 'Delivery Fee',
    'real_delivery_fee' => 'Real Delivery Fee',
    'display_delivery_fee' => 'Display Delivery Fee',
    'coupon_discount' => 'Coupon Discount',
    'free_delivery_discount' => 'Free Delivery',
    'actual_charge' => 'Actual Charge',
    'total_price' => 'Total Price',
    'profit' => 'Profit',
    'profit_margin' => 'Profit Margin',
    'order_date' => 'Order Date',

    // Order Statuses
    'status_processing' => 'Processing',
    'status_with_delivery_company' => 'With Delivery',
    'status_received' => 'Received',
    'status_cancelled' => 'Cancelled',
    'status_returned' => 'Returned',
    'all_statuses' => 'All Statuses',

    // Order Actions
    'create_order' => 'Create Order',
    'edit_order' => 'Edit Order',
    'delete_order' => 'Delete Order',
    'update_order' => 'Update Order',
    'view_order' => 'View Order',
    'assign_courier' => 'Assign Courier',
    'update_status' => 'Update Status',
    'order_not_editable' => 'This order cannot be edited',
    'order_cannot_be_deleted' => 'This order cannot be deleted',

    // Order Success Messages
    'order_created_successfully' => 'Order created successfully.',
    'order_updated_successfully' => 'Order updated successfully.',
    'order_deleted_successfully' => 'Order deleted successfully.',
    'order_status_updated' => 'Order status updated to ":status".',
    'courier_assigned_successfully' => 'Courier assigned successfully.',

    // Order Empty States
    'no_orders_found' => 'No orders found',
    'no_orders_description' => 'No orders have been placed yet',
    'no_order_items_found' => 'No items in this order',

    // Search & Filter Orders
    'search_orders' => 'Search by order number or customer name',
    'filter_by_status' => 'Filter by Status',
    'filter_by_city' => 'Filter by City',
    'filter_by_courier' => 'Filter by Courier',
    'filter_by_coupon' => 'Filter by Coupon',
    'all_couriers' => 'All Couriers',
    'all_coupons' => 'All Coupons',

    // Order Items
    'item_product' => 'Product',
    'item_quantity' => 'Quantity',
    'item_base_price' => 'Base Price',
    'item_discount' => 'Discount per Unit',
    'item_final_price' => 'Final Price',
    'item_total' => 'Total',
    'item_cost_price' => 'Cost Price',
    'item_profit' => 'Item Profit',

    // Order Validation Messages
    'validation_order_number_required' => 'The order number field is required.',
    'validation_full_name_required' => 'The customer name field is required.',
    'validation_city_id_required' => 'The city field is required.',
    'validation_address_required' => 'The address field is required.',
    'validation_phone_numbers_required' => 'At least one phone number is required.',
    'validation_status_required' => 'The status field is required.',
    'validation_status_in' => 'The selected status is invalid.',
    'validation_courier_id_exists' => 'The selected courier is invalid.',
    'validation_courier_id_required' => 'The courier field is required.',

    // Order Status Transitions
    'status_transition_not_allowed' => 'Cannot transition from current status to selected status.',
    'allowed_transitions' => 'Allowed Status Transitions',

    // Order Profit
    'order_profit' => 'Order Profit',
    'total_items' => 'Total Items',

    // ============ COUPON MANAGEMENT ============
    'coupons_management' => 'Coupons Management',
    'coupons_description' => 'Manage discount coupons and promotions',
    'coupon_code' => 'Coupon Code',
    'coupon_type' => 'Coupon Type',
    'coupon_value' => 'Coupon Value',
    'coupon_minimum_order' => 'Minimum Order Amount',
    'coupon_max_uses' => 'Maximum Uses',
    'coupon_used_count' => 'Used Count',
    'coupon_remaining_uses' => 'Remaining Uses',
    'coupon_valid_from' => 'Valid From',
    'coupon_valid_until' => 'Valid Until',
    'coupon_is_active' => 'Status',
    'coupon_details' => 'Coupon Details',
    'coupons_list' => 'Coupons List',

    // Coupon Types
    'type_fixed' => 'Fixed Amount',
    'type_percentage' => 'Percentage',
    'type_free_delivery' => 'Free Delivery',
    'all_types' => 'All Types',

    // Coupon Actions
    'create_coupon' => 'Create Coupon',
    'edit_coupon' => 'Edit Coupon',
    'delete_coupon' => 'Delete Coupon',
    'update_coupon' => 'Update Coupon',
    'view_coupon' => 'View Coupon',
    'deactivate_coupon' => 'Deactivate',
    'activate_coupon' => 'Activate',

    // Coupon Success Messages
    'coupon_created_successfully' => 'Coupon created successfully.',
    'coupon_updated_successfully' => 'Coupon updated successfully.',
    'coupon_deleted_successfully' => 'Coupon deleted successfully.',
    'coupon_activated' => 'Coupon activated successfully.',
    'coupon_deactivated' => 'Coupon deactivated successfully.',

    // Coupon Empty States
    'no_coupons_found' => 'No coupons found',
    'no_coupons_description' => 'No coupons have been created yet',
    'create_first_coupon' => 'Create First Coupon',

    // Search & Filter Coupons
    'search_coupons' => 'Search by coupon code',
    'filter_by_type' => 'Filter by Type',
    'filter_by_status' => 'Filter by Status',
    'filter_active' => 'Active',
    'filter_inactive' => 'Inactive',

    // Coupon Validation Messages
    'validation_code_required' => 'The coupon code field is required.',
    'validation_code_unique' => 'This coupon code already exists.',
    'validation_code_max' => 'The coupon code must not exceed 50 characters.',
    'validation_type_required' => 'The coupon type field is required.',
    'validation_type_in' => 'The selected type is invalid.',
    'validation_value_required' => 'The coupon value field is required.',
    'validation_value_numeric' => 'The value must be a number.',
    'validation_value_min' => 'The value must be at least 0.',
    'validation_value_max_percentage' => 'Percentage value cannot exceed 100.',
    'validation_minimum_order_numeric' => 'Minimum order amount must be a number.',
    'validation_minimum_order_min' => 'Minimum order amount must be at least 0.',
    'validation_max_uses_integer' => 'Maximum uses must be an integer.',
    'validation_max_uses_min' => 'Maximum uses must be at least 1.',
    'validation_valid_from_required' => 'The valid from date is required.',
    'validation_valid_from_date' => 'The valid from date must be a valid date.',
    'validation_valid_until_after' => 'The valid until date must be after valid from date.',
    'validation_code_format' => 'Coupon code can only contain letters, numbers, and underscores.',

    // Coupon Help Text
    'coupon_code_help' => 'Unique code that customers will enter (e.g., SUMMER2024)',
    'coupon_type_help' => 'Choose how the discount will be applied',
    'coupon_value_help' => 'Fixed amount (JOD) or percentage (0-100)',
    'coupon_minimum_order_help' => 'Minimum cart total required to use this coupon',
    'coupon_max_uses_help' => 'Leave empty for unlimited uses',
    'coupon_valid_from_help' => 'When the coupon becomes valid',
    'coupon_valid_until_help' => 'Leave empty for no expiration',

    // Coupon Usage Info
    'coupon_usage_info' => 'Usage Information',
    'coupon_times_used' => 'Times Used',
    'coupon_orders_count' => 'Orders Using This Coupon',
    'coupon_discount_given' => 'Total Discount Given',

    // Coupon Status
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'coupon_is_expired' => 'Expired',
    'coupon_not_started' => 'Not Yet Started',
    'coupon_unlimited' => 'Unlimited',

    // Confirmation Messages
    'confirm_delete_coupon' => 'Are you sure you want to delete this coupon?',
    'confirm_deactivate_coupon' => 'Are you sure you want to deactivate this coupon?',

    // Coupon Affects
    'coupon_applies_to' => 'Applies To',
    'applies_to_products' => 'Products',
    'applies_to_delivery' => 'Delivery',

    // Additional Help Text
    'is_active_help' => 'Whether this coupon is currently available for customers',
    'coupon_already_used' => 'This coupon has been used :count times. Editing it will affect existing orders.',
    'create_coupon_description' => 'Create a new discount coupon for customers',
    'edit_coupon_description' => 'Update coupon details and restrictions',
    'cannot_delete_used_coupon' => 'Cannot delete coupon that has been used in :count order(s).',

    // Validation for minimum_order_amount
    'validation_minimum_order_required' => 'The minimum order amount field is required.',
];
