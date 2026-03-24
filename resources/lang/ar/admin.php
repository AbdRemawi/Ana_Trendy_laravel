<?php

return [
    // Page Titles
    'roles_management' => 'إدارة الأدوار',
    'permissions_management' => 'إدارة الصلاحيات',
    'users_management' => 'إدارة المستخدمين',
    'brands_management' => 'إدارة الماركات',
    'roles' => 'الأدوار',
    'permissions' => 'الصلاحيات',
    'users' => 'المستخدمين',
    'brands' => 'الماركات',

    // Descriptions
    'roles_description' => 'إدارة أدوار المستخدمين وصلاحياتهم',
    'permissions_description' => 'إدارة صلاحيات النظام',
    'users_description' => 'إدارة مستخدمي النظام وحساباتهم',
    'brands_description' => 'إدارة ماركات المنتجات',

    // CRUD Actions
    'create_role' => 'إنشاء دور',
    'edit_role' => 'تعديل الدور',
    'delete_role' => 'حذف الدور',
    'update_role' => 'تحديث الدور',
    'sync_permissions' => 'مزامنة الصلاحيات',

    'create_permission' => 'إنشاء صلاحية',
    'edit_permission' => 'تعديل الصلاحية',
    'delete_permission' => 'حذف الصلاحية',
    'update_permission' => 'تحديث الصلاحية',

    'create_user' => 'إنشاء مستخدم',
    'edit_user' => 'تعديل المستخدم',
    'delete_user' => 'حذف المستخدم',
    'update_user' => 'تحديث المستخدم',

    'create_brand' => 'إنشاء ماركة',
    'edit_brand' => 'تعديل الماركة',
    'delete_brand' => 'حذف الماركة',
    'update_brand' => 'تحديث الماركة',

    // Common Labels
    'actions' => 'الإجراءات',
    'view' => 'عرض',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'cancel' => 'إلغاء',
    'save' => 'حفظ',
    'back' => 'رجوع',

    // Role Labels
    'role_name' => 'اسم الدور',
    'role_name_placeholder' => 'مثال: مدير_المحتوى',
    'role_details' => 'تفاصيل الدور',
    'permissions_count' => 'الصلاحيات',
    'assigned' => 'مخصص',
    'no_permissions' => 'لا توجد صلاحيات',
    'no_permissions_assigned' => 'لم يتم تعيين صلاحيات لهذا الدور',

    // Permission Labels
    'permission_name' => 'اسم الصلاحية',
    'permission_name_help' => 'استخدم أحرف صغيرة أو مسافات أو شرطات سفلية (مثال: "إدارة المنتجات")',
    'permission_details' => 'تفاصيل الصلاحية',
    'roles_count' => 'مستخدم بواسطة',
    'role' => 'دور',
    'roles' => 'أدوار',
    'not_used' => 'غير مستخدم',
    'not_assigned_to_any_role' => 'لم يتم تعيينه لأي دور',

    // Dates
    'created_at' => 'تاريخ الإنشاء',
    'updated_at' => 'تاريخ التحديث',

    // Empty States
    'no_roles_found' => 'لم يتم العثور على أدوار',
    'no_roles_description' => 'ابدأ بإنشاء أول دور لك',
    'create_first_role' => 'إنشاء أول دور',

    'no_permissions_found' => 'لم يتم العثور على صلاحيات',
    'no_permissions_description' => 'ابدأ بإنشاء أول صلاحية لك',
    'create_first_permission' => 'إنشاء أول صلاحية',

    // Confirmations
    'confirm_delete_role' => 'هل أنت متأكد من حذف هذا الدور',
    'confirm_delete_permission' => 'هل أنت متأكد من حذف هذه الصلاحية',

    // Role Edit/Create
    'create_role_description' => 'إنشاء دور جديد وتعيين الصلاحيات',
    'edit_role_description' => 'تحديث اسم الدور وصلاحياته',
    'permissions_after_creation' => 'تعيين الصلاحيات بعد الإنشاء',
    'permissions_after_creation_description' => 'قم بإنشاء الدور أولاً، ثم يمكنك تعيين الصلاحيات له.',
    'next_step' => 'الخطوة التالية',
    'after_role_created' => 'بعد إنشاء الدور، يمكنك تعيين الصلاحيات له.',
    'toggle_permissions_help' => 'بدّل الصلاحيات لمنح أو إلغاء الوصول',
    'information' => 'المعلومات',
    'dashboard' => 'لوحة التحكم',
    'users' => 'المستخدمين',
    'products' => 'المنتجات',
    'orders' => 'الطلبات',
    'affiliate' => 'التابعين',
    'system' => 'النظام',

    // Permission Edit/Create
    'create_permission_description' => 'إنشاء صلاحية نظام جديدة',
    'auto_format' => 'تنسيق تلقائي',
    'permission_auto_format_description' => 'سيتم تحويل أسماء الصلاحيات تلقائياً إلى تنسيق snake_case.',
    'in_use' => 'قيد الاستخدام',
    'permission_in_use_description' => 'هذه الصلاحية معينة لـ :count دور (أدوار). تغييرها س يؤثر على جميع الأدوار المعينة.',
    'roles_using_permission' => 'الأدوار التي تستخدم هذه الصلاحية',
    'assigned_roles' => 'الأدوار المعينة',

    // Special Roles
    'super_admin' => 'مسؤول متميز',

    // Warnings
    'cannot_delete_used_permission' => 'لا يمكن حذف صلاحية قيد الاستخدام',
    'cannot_delete_super_admin' => 'لا يمكن حذف دور مسؤول متميز',

    // Pagination
    'showing' => 'عرض :from إلى :to من أصل :total نتيجة',

    // ============ USER MANAGEMENT ============
    'user_name' => 'الاسم',
    'user_mobile' => 'رقم الجوال',
    'user_email' => 'البريد الإلكتروني',
    'user_password' => 'كلمة المرور',
    'user_role' => 'الدور',
    'user_status' => 'الحالة',
    'user_details' => 'تفاصيل المستخدم',

    'mobile_placeholder' => 'مثال: 0791234567',
    'mobile_help' => 'يجب أن يكون 10 أرقام يبدأ بـ 078 أو 079 أو 077',

    'password_required' => 'كلمة المرور مطلوبة',
    'password_optional' => 'اتركه فارغاً للحفاظ على كلمة المرور الحالية',
    'password_placeholder' => 'أدخل 8 أحرف على الأقل',

    'status_active' => 'نشط',
    'status_inactive' => 'غير نشط',
    'status_suspended' => 'معلق',

    'create_user_description' => 'إنشاء حساب مستخدم جديد وتعيين دور',
    'edit_user_description' => 'تحديث تفاصيل المستخدم وتعيين الدور',

    // User Success Messages
    'user_created_successfully' => 'تم إنشاء المستخدم بنجاح.',
    'user_updated_successfully' => 'تم تحديث المستخدم بنجاح.',
    'user_deleted_successfully' => 'تم حذف المستخدم ":name" بنجاح.',

    // User Error Messages
    'cannot_delete_super_admin_user' => 'لا يمكن حذف مستخدمي مسؤول متميز.',
    'cannot_delete_own_account' => 'لا يمكن حذف حسابك الخاص.',
    'cannot_modify_own_role' => 'لا يمكن تعديل دورك الخاص.',
    'cannot_assign_super_admin' => 'يمكن فقط للمسؤولين المتميزين تعيين دور مسؤول متميز.',

    // Role Success Messages
    'role_created' => 'تم إنشاء الدور ":name" بنجاح.',
    'role_updated' => 'تم تحديث الدور بنجاح.',
    'role_deleted' => 'تم حذف الدور ":name" بنجاح.',
    'role_deleted_with_users' => 'تم حذف الدور ":name" بنجاح. تمت إزالته من :count مستخدم.',
    'cannot_delete_role_with_users' => 'لا يمكن حذف دور معين لـ :count مستخدم (مستخدمين). يرجى إعادة تعيين المستخدمين أولاً.',

    // Permission Success Messages
    'permission_created' => 'تم إنشاء الصلاحية ":name" بنجاح.',
    'permission_updated' => 'تم تحديث الصلاحية بنجاح.',
    'permission_deleted' => 'تم حذف الصلاحية ":name" بنجاح.',
    'cannot_delete_permission_in_use' => 'لا يمكن حذف صلاحية معينة للأدوار: :roles.',

    // User Empty States
    'no_users_found' => 'لم يتم العثور على مستخدمين',
    'no_users_description' => 'ابدأ بإنشاء أول مستخدم لك',
    'create_first_user' => 'إنشاء أول مستخدم',

    'confirm_delete_user' => 'هل أنت متأكد من حذف هذا المستخدم',

    // Search & Filter
    'search_users' => 'البحث بالاسم أو البريد أو الجوال',
    'filter_by_role' => 'تصفية حسب الدور',
    'all_roles' => 'جميع الأدوار',

    // ============ BRAND MANAGEMENT ============
    'brand_name' => 'اسم الماركة',
    'brand_slug' => 'الرابط المختصر',
    'brand_logo' => 'الشعار',
    'brand_status' => 'الحالة',
    'brand_details' => 'تفاصيل الماركة',
    'brands_list' => 'قائمة الماركات',

    'brand_name_placeholder' => 'مثال: نايكي',
    'brand_logo_help' => 'تحميل شعار الماركة (JPEG، PNG، أو WEBP، حد أقصى 2 ميجابايت)',

    'create_brand_description' => 'إنشاء ماركة جديدة',
    'edit_brand_description' => 'تحديث تفاصيل الماركة',

    // Brand Success Messages
    'brand_created_successfully' => 'تم إنشاء الماركة بنجاح.',
    'brand_updated_successfully' => 'تم تحديث الماركة بنجاح.',
    'brand_deleted_successfully' => 'تم حذف الماركة ":name" بنجاح.',
    'brand_restored_successfully' => 'تم استعادة الماركة بنجاح.',

    // Brand Empty States
    'no_brands_found' => 'لم يتم العثور على ماركات',
    'no_brands_description' => 'ابدأ بإنشاء أول ماركة لك',
    'create_first_brand' => 'إنشاء أول ماركة',

    'confirm_delete_brand' => 'هل أنت متأكد من حذف هذه الماركة',

    // Search & Filter Brands
    'search_brands' => 'البحث باسم الماركة',
    'filter_by_status' => 'تصفية حسب الحالة',
    'all_statuses' => 'جميع الحالات',
    'search' => 'البحث',

    // ============ CATEGORY MANAGEMENT ============
    'categories_management' => 'إدارة التصنيفات',
    'categories' => 'التصنيفات',
    'categories_description' => 'إدارة تصنيفات المنتجات بشكل هرمي',

    'category_name' => 'اسم التصنيف',
    'category_slug' => 'الرابط المختصر',
    'category_parent' => 'التصنيف الأب',
    'category_image' => 'الصورة',
    'category_status' => 'الحالة',
    'category_sort_order' => 'ترتيب العرض',
    'category_details' => 'تفاصيل التصنيف',
    'categories_list' => 'قائمة التصنيفات',

    'category_name_placeholder' => 'مثال: إلكترونيات',
    'category_image_help' => 'تحميل صورة التصنيف (JPEG، PNG، أو WEBP، حد أقصى 2 ميجابايت)',

    'create_category' => 'إنشاء تصنيف',
    'edit_category' => 'تعديل التصنيف',
    'delete_category' => 'حذف التصنيف',
    'update_category' => 'تحديث التصنيف',

    'create_category_description' => 'إنشاء تصنيف جديد',
    'edit_category_description' => 'تحديث تفاصيل التصنيف',

    'no_parent' => 'بدون أب',
    'select_parent_category' => 'اختر التصنيف الأب (اختياري)',

    // Category Success Messages
    'category_created_successfully' => 'تم إنشاء التصنيف بنجاح.',
    'category_updated_successfully' => 'تم تحديث التصنيف بنجاح.',
    'category_deleted_successfully' => 'تم حذف التصنيف بنجاح.',
    'category_restored_successfully' => 'تم استعادة التصنيف بنجاح.',
    'category_has_children' => 'لا يمكن حذف تصنيف يحتوي على أقسام فرعية',

    // Category Empty States
    'no_categories_found' => 'لم يتم العثور على تصنيفات',
    'no_categories_description' => 'ابدأ بإنشاء أول تصنيف لك',
    'create_first_category' => 'إنشاء أول تصنيف',

    'confirm_delete_category' => 'هل أنت متأكد من حذف هذا التصنيف',

    // Search & Filter Categories
    'search_categories' => 'البحث باسم التصنيف',
    'clear' => 'مسح',

    // ============ PRODUCT MANAGEMENT ============
    'products_management' => 'إدارة المنتجات',
    'products' => 'المنتجات',
    'products_description' => 'إدارة كتالوج المنتجات مع الصور والمخزون',

    'product_name' => 'اسم المنتج',
    'product_slug' => 'الرابط المختصر',
    'product_brand' => 'الماركة',
    'product_category' => 'التصنيف',
    'product_description' => 'الوصف',
    'product_size' => 'المقاس',
    'product_gender' => 'الجنس',
    'product_cost_price' => 'سعر التكلفة',
    'product_sale_price' => 'سعر البيع',
    'product_offer_price' => 'سعر العرض',
    'product_status' => 'الحالة',
    'product_stock' => 'المخزون',
    'product_details' => 'تفاصيل المنتج',
    'products_list' => 'قائمة المنتجات',

    'product_name_placeholder' => 'مثال: تيشيرت قطني كلاسيكي',
    'product_description_placeholder' => 'أدخل وصف المنتج...',
    'product_size_help' => 'اختر المقاس: S، M، L، XL، أو XXL',
    'product_gender_help' => 'اختر الجنس المستهدف: ذكر، أنثى، أو للجميع',

    // Product Sizes
    'size_s' => 'S',
    'size_m' => 'M',
    'size_l' => 'L',
    'size_xl' => 'XL',
    'size_xxl' => 'XXL',
    'all_sizes' => 'جميع المقاسات',

    // Product Genders
    'gender_male' => 'ذكر',
    'gender_female' => 'أنثى',
    'gender_unisex' => 'للجميع',
    'all_genders' => 'جميع الأجناس',

    'create_product' => 'إنشاء منتج',
    'edit_product' => 'تعديل المنتج',
    'delete_product' => 'حذف المنتج',
    'update_product' => 'تحديث المنتج',

    'create_product_description' => 'إنشاء منتج جديد',
    'edit_product_description' => 'تحديث تفاصيل المنتج',

    // Product Success Messages
    'product_created_successfully' => 'تم إنشاء المنتج بنجاح.',
    'product_updated_successfully' => 'تم تحديث المنتج بنجاح.',
    'product_deleted_successfully' => 'تم حذف المنتج ":name" بنجاح.',
    'product_restored_successfully' => 'تم استعادة المنتج بنجاح.',

    // Product Empty States
    'no_products_found' => 'لم يتم العثور على منتجات',
    'no_products_description' => 'ابدأ بإنشاء أول منتج لك',
    'create_first_product' => 'إنشاء أول منتج',

    'confirm_delete_product' => 'هل أنت متأكد من حذف هذا المنتج',

    // Search & Filter Products
    'search_products' => 'البحث باسم المنتج',
    'filter_by_brand' => 'تصفية حسب الماركة',
    'filter_by_category' => 'تصفية حسب التصنيف',
    'filter_by_size' => 'تصفية حسب المقاس',
    'filter_by_gender' => 'تصفية حسب الجنس',
    'all_brands' => 'جميع الماركات',
    'all_categories' => 'جميع التصنيفات',

    // ============ PRODUCT IMAGES ============
    'product_images' => 'صور المنتج',
    'product_image' => 'صورة المنتج',
    'product_images_management' => 'إدارة صور المنتجات',
    'product_images_description' => 'إدارة معرض صور المنتجات',

    'image_file' => 'ملف الصورة',
    'is_primary' => 'الصورة الرئيسية',
    'sort_order' => 'ترتيب العرض',
    'image_details' => 'تفاصيل الصورة',

    'set_primary_image' => 'تعيين كرئيسية',
    'upload_image' => 'تحميل صورة',
    'upload_images' => 'تحميل الصور',
    'manage_images' => 'إدارة الصور',
    'image_gallery' => 'معرض الصور',

    'product_image_help' => 'تحميل صورة المنتج (JPEG، PNG، أو WEBP، حد أقصى 2 ميجابايت)',

    // Product Image Success Messages
    'product_image_created_successfully' => 'تم إنشاء صورة المنتج بنجاح.',
    'product_image_updated_successfully' => 'تم تحديث صورة المنتج بنجاح.',
    'product_image_deleted_successfully' => 'تم حذف صورة المنتج بنجاح.',
    'product_image_set_primary_successfully' => 'تم تحديث الصورة الرئيسية بنجاح.',
    'product_images_reordered_successfully' => 'تم إعادة ترتيب الصور بنجاح.',

    'confirm_delete_product_image' => 'هل أنت متأكد من حذف هذه الصورة',
    'no_images_found' => 'لم يتم العثور على صور',
    'no_images_description' => 'قم بتحميل صور لهذا المنتج',

    // ============ INVENTORY MANAGEMENT ============
    'inventory_management' => 'إدارة المخزون',
    'inventory' => 'المخزون',
    'inventory_description' => 'تتبع حركات المخزون والمعاملات',

    'transaction_type' => 'نوع المعاملة',
    'transaction_quantity' => 'الكمية',
    'transaction_notes' => 'ملاحظات',
    'transaction_date' => 'التاريخ',

    // Transaction Types
    'type_supply' => 'توريد',
    'type_sale' => 'بيع',
    'type_return' => 'إرجاع',
    'type_damage' => 'تالف',
    'type_adjustment' => 'تعديل',
    'all_types' => 'جميع الأنواع',

    'create_transaction' => 'إنشاء معاملة',
    'edit_transaction' => 'تعديل المعاملة',
    'delete_transaction' => 'حذف المعاملة',

    'create_transaction_description' => 'تسجيل حركة مخزون',
    'transaction_type_help' => 'اختر نوع حركة المخزون',
    'transaction_quantity_help' => 'أدخل الكمية (رقم موجب)',
    'transaction_notes_help' => 'أضف ملاحظات اختيارية للمرجع',

    // Inventory Success Messages
    'inventory_transaction_created_successfully' => 'تم إنشاء معاملة المخزون بنجاح.',
    'inventory_transaction_updated_successfully' => 'تم تحديث معاملة المخزون بنجاح.',
    'inventory_transaction_deleted_successfully' => 'تم حذف معاملة المخزون بنجاح.',

    'confirm_delete_inventory_transaction' => 'هل أنت متأكد من حذف هذه المعاملة',
    'no_transactions_found' => 'لم يتم العثور على معاملات',
    'product_inventory' => 'مخزون المنتج',
    'view_inventory' => 'عرض المخزون',
    'current_stock' => 'المخزون الحالي',

    // ============ PRICES & DISCOUNTS ============
    'price' => 'السعر',
    'prices' => 'الأسعار',
    'cost_price_help' => 'سعر التكلفة لشراء/إنتاج المنتج',
    'sale_price_help' => 'سعر البيع العادي (يجب أن يكون أكبر من سعر التكلفة)',
    'offer_price_help' => 'سعر ترويجي اختياري (يجب أن يكون أقل من سعر البيع)',
    'profit_margin' => 'هامش الربح',
    'has_offer' => 'هناك عرض',
    'effective_price' => 'السعر الفعلي',

    // ============ MISSING KEYS ============
    'main_navigation' => 'القائمة الرئيسية',
    'users_table' => 'جدول المستخدمين',
    'user_actions' => 'إجراءات المستخدم',
    'you' => 'أنت',
    'not_provided' => 'غير متوفر',
    'no_role' => 'لا يوجد دور',
    'toggle_permission' => 'تبديل الصلاحية: :permission',

    // ============ COMMON LABELS ============
    'optional' => 'اختياري',
    'note' => 'ملاحظة',
    'attributes' => 'الخصائص',
    'select_brand' => 'اختر الماركة',
    'select_category' => 'اختر التصنيف',
    'select_product' => 'اختر المنتج',
    'select_size' => 'اختر المقاس',
    'select_gender' => 'اختر الجنس',

    // ============ PRODUCT IMAGES ============
    'primary_image_notice' => 'سيتم تعيين الصورة الأولى كصورة رئيسية. يمكنك تغيير ذلك في صفحة التعديل.',
    'max_10_images' => 'حد أقصى 10 صور',
    'images_help' => 'الصيغ المسموحة: JPEG، PNG، WebP. حد أقصى 2 ميجابايت لكل صورة.',
    'image_preview' => 'معاينة الصورة',
    'no_images_uploaded' => 'لم يتم تحميل صور بعد',
    'primary' => 'رئيسي',
    'select_primary_image_help' => 'انقر على صورة لتعيينها كصورة رئيسية.',
    'upload_new_images' => 'تحميل صور جديدة',
    'remove_image' => 'إزالة الصورة',
    'confirm_remove_image' => 'هل أنت متأكد من إزالة هذه الصورة؟',
    'new_images_will_add' => 'سيتم تحميل الصور الجديدة وإضافتها عند الحفظ. يمكنك بعد ذلك اختيار الصورة الرئيسية.',
    'at_least_one_image_required' => 'يجب أن تكون هناك صورة واحدة على الأقل.',
    'select_primary_image' => 'الرجاء اختيار صورة رئيسية.',
    'initial_supply_transaction' => 'المخزون الأولي عند إنشاء المنتج',

    // ============ PRODUCT HELP TEXT ============
    'product_name_help' => 'سيتم استخدام اسم المنتج لإنشاء رابط فريد تلقائياً.',

    // ============ INITIAL INVENTORY ============
    'initial_inventory' => 'المخزون الأولي',
    'initial_inventory_help' => 'حدد كمية المخزون الافتتاحي لهذا المنتج.',
    'initial_stock_quantity' => 'كمية المخزون الأولي',
    'initial_stock_help' => 'سيتم تسجيل هذه الكمية كمعاملة توريد أولية.',
    'initial_stock_note' => 'يمكن تعديل المخزون لاحقاً من خلال إدارة المخزون.',

    // ============ INVENTORY MESSAGES ============
    'no_transactions_description' => 'لم يتم تسجيل معاملات مخزون بعد',

    // Transaction Type Labels
    'transaction_types' => 'أنواع المعاملات',
    'select_type' => 'اختر النوع',
    'supply_desc' => 'إضافة مخزون',
    'sale_desc' => 'تسجيل عملية بيع (تخصم من المخزون)',
    'return_desc' => 'إرجاع من العميل (يضاف للمخزون)',
    'damage_desc' => 'بضاعة تالفة (تخصم من المخزون)',
    'adjustment_desc' => 'تصحيح يدوي للمخزون',

    // Inventory Validation Messages
    'quantity_positive_for_type' => 'يجب أن تكون الكمية رقماً موجباً لمعاملات :type.',
    'inventory_would_go_negative' => 'هذه المعاملة ستؤدي إلى مخزون سالب (الحالي: :current_stock، المتوقع: :projected_stock، المطلوب: :requested_quantity).',
    'adjustment_would_go_negative' => 'هذا التعديل سيؤدي إلى مخزون سالب (الحالي: :current_stock، التعديل: :adjustment_value).',

    // ============ VALIDATION MESSAGES ============
    // Brand
    'validation_brand_required' => 'حقل الماركة مطلوب.',
    'validation_brand_exists' => 'الماركة المحددة غير صالحة.',

    // Category
    'validation_category_required' => 'حقل التصنيف مطلوب.',
    'validation_category_exists' => 'التصنيف المحدد غير صالح.',

    // Product Name
    'validation_name_required' => 'حقل اسم المنتج مطلوب.',
    'validation_name_max' => 'اسم المنتج لا يجب أن يتجاوز 255 حرفاً.',

    // Description
    'validation_description_max' => 'الوصف لا يجب أن يتجاوز 5000 حرف.',

    // Size
    'validation_size_required' => 'حقل المقاس مطلوب.',
    'validation_size_in' => 'المقاس المحدد غير صالح.',

    // Gender
    'validation_gender_required' => 'حقل الجنس مطلوب.',
    'validation_gender_in' => 'الجنس المحدد غير صالح.',

    // Cost Price
    'validation_cost_price_required' => 'حقل سعر التكلفة مطلوب.',
    'validation_cost_price_decimal' => 'سعر التكلفة يجب أن يكون رقماً عشرياً صالحاً.',
    'validation_cost_price_min' => 'سعر التكلفة يجب أن يكون 0 على الأقل.',
    'validation_cost_price_max' => 'سعر التكلفة لا يجب أن يتجاوز 99999999.99.',

    // Sale Price
    'validation_sale_price_required' => 'حقل سعر البيع مطلوب.',
    'validation_sale_price_decimal' => 'سعر البيع يجب أن يكون رقماً عشرياً صالحاً.',
    'validation_sale_price_min' => 'سعر البيع يجب أن يكون 0 على الأقل.',
    'validation_sale_price_max' => 'سعر البيع لا يجب أن يتجاوز 99999999.99.',
    'validation_sale_price_gt' => 'سعر البيع يجب أن يكون أكبر من سعر التكلفة.',

    // Offer Price
    'validation_offer_price_decimal' => 'سعر العرض يجب أن يكون رقماً عشرياً صالحاً.',
    'validation_offer_price_min' => 'سعر العرض يجب أن يكون 0 على الأقل.',
    'validation_offer_price_max' => 'سعر العرض لا يجب أن يتجاوز 99999999.99.',
    'validation_offer_price_lt' => 'سعر العرض يجب أن يكون أقل من سعر البيع.',

    // Status
    'validation_status_required' => 'حقل الحالة مطلوب.',
    'validation_status_in' => 'الحالة المحددة غير صالحة.',

    // Initial Quantity
    'validation_initial_quantity_required' => 'حقل كمية المخزون الأولي مطلوب.',
    'validation_initial_quantity_integer' => 'كمية المخزون الأولي يجب أن تكون عدداً صحيحاً.',
    'validation_initial_quantity_min' => 'كمية المخزون الأولي يجب أن تكون 0 على الأقل.',

    // Images
    'validation_images_required' => 'يجب تحميل صورة واحدة على الأقل للمنتج.',
    'validation_images_array' => 'الصور يجب أن تكون مصفوفة.',
    'validation_images_min' => 'يجب تحميل صورة واحدة على الأقل للمنتج.',
    'validation_images_max' => 'لا يمكن تحميل أكثر من 10 صور.',
    'validation_images_required_file' => 'ملف الصورة مطلوب.',
    'validation_images_image' => 'الملف المحمل يجب أن يكون صورة.',
    'validation_images_mimes' => 'الصور يجب أن تكون من الأنواع: jpeg، png، jpg، webp.',
    'validation_images_max_file' => 'كل صورة لا يجب أن تتجاوز 2 ميجابايت.',

    // Primary Image ID
    'validation_primary_image_id_integer' => 'اختيار الصورة الرئيسية غير صالح.',
    'validation_primary_image_id_exists' => 'الصورة الرئيسية المحددة غير موجودة.',
    'validation_primary_image_removed' => 'الصورة الرئيسية المحددة يتم إزالتها. الرجاء اختيار صورة رئيسية أخرى.',
    'validation_primary_image_invalid' => 'الصورة الرئيسية المحددة غير صالحة.',

    // Remove Images
    'validation_remove_images_integer' => 'معرف الصورة المراد إزالتها يجب أن يكون عدداً صحيحاً.',
    'validation_remove_images_exists' => 'معرف الصورة المراد إزالتها غير موجود.',

    // Product Inventory
    'validation_product_required' => 'حقل المنتج مطلوب.',
    'validation_product_exists' => 'المنتج المحدد غير صالح.',

    // Transaction Type
    'validation_type_required' => 'حقل نوع المعاملة مطلوب.',
    'validation_type_in' => 'نوع المعاملة يجب أن يكون واحداً من: توريد، بيع، إرجاع، تالف، تعديل.',

    // Transaction Quantity
    'validation_quantity_required' => 'حقل الكمية مطلوب.',
    'validation_quantity_integer' => 'الكمية يجب أن تكون عدداً صحيحاً.',
    'validation_quantity_min' => 'الكمية لا يجب أن تكون أقل من -999999.',
    'validation_quantity_max' => 'الكمية لا يجب أن تتجاوز 999999.',

    // Notes
    'validation_notes_max' => 'الملاحظات لا يجب أن تتجاوز 1000 حرف.',

    // ============ VALIDATION ATTRIBUTES ============
    "attribute_brand" => "الماركة",
    "attribute_category" => "التصنيف",
    "attribute_product_name" => "اسم المنتج",
    "attribute_description" => "الوصف",
    "attribute_size" => "المقاس",
    "attribute_gender" => "الجنس",
    "attribute_cost_price" => "سعر التكلفة",
    "attribute_sale_price" => "سعر البيع",
    "attribute_offer_price" => "سعر العرض",
    "attribute_status" => "الحالة",
    "attribute_initial_stock_quantity" => "كمية المخزون الأولي",
    "attribute_images" => "صور المنتج",
    "attribute_primary_image" => "الصورة الرئيسية",
    "attribute_remove_images" => "الصور المراد إزالتها",
    "attribute_product" => "المنتج",
    "attribute_transaction_type" => "نوع المعاملة",
    "attribute_quantity" => "الكمية",
    "attribute_notes" => "الملاحظات",

    // ============ ROLE VALIDATION MESSAGES ============
    "validation_name_required" => "حقل اسم الدور مطلوب.",
    "validation_name_min" => "يجب أن يكون اسم الدور حرفين على الأقل.",
    "validation_name_max" => "يجب أن لا يتجاوز اسم الدور 50 حرفاً.",
    "validation_name_regex" => "يجب أن يحتوي اسم الدور على أحرف صغيرة وأرقام وشرطات سفلية فقط.",
    "validation_name_unique" => "اسم الدور موجود بالفعل.",
    "validation_permissions_array" => "يجب أن تكون الصلاحيات مصفوفة من أسماء الصلاحيات.",
    "validation_permissions_exists" => "واحدة أو أكثر من الصلاحيات المحددة غير موجودة.",

    // ============ ROLE EDIT SPECIFIC ============
    "role_name_readonly" => "لا يمكن تغيير اسم الدور بعد الإنشاء.",
    "dashboard_permission_required" => "مطلوبة دائماً للوصول إلى لوحة التحكم.",

    // ============ DELETE ROLE MODAL ============
    "delete_role_title" => "حذف الدور",
    "delete_role_warning" => "هل أنت متأكد من حذف الدور ':role'؟ لا يمكن التراجع عن هذا الإجراء.",
    "confirm_delete" => "نعم، احذف الدور",
    "delete_role_confirmation" => "سيتم حذف هذا الدور نهائياً.",
    "irreversible_action" => "هذا الإجراء لا يمكن التراجع عنه وسيؤدي إلى حذف الدور نهائياً.",
    "processing" => "جاري المعالجة...",

    // ============ DELETE USER MODAL ============
    "delete_user_title" => "حذف المستخدم",
    "delete_user_warning" => "هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.",
    "confirm_delete_user" => "نعم، احذف المستخدم",
    "delete_user_confirmation" => "سيتم حذف هذا المستخدم نهائياً.",

    // ============ ADDITIONAL USER MANAGEMENT TRANSLATIONS ============
    "back_to_users" => "العودة للمستخدمين",
    "viewing_user_details" => "عرض تفاصيل :name",
    "commission_rate" => "معدل العمولة",
    "commission_rate_help" => "نسبة العمولة للتابعين (0-100)",
    "role_assignment_help" => "قم بتعيين دور لتحديد صلاحيات المستخدم.",
    "current_user_info" => "معلومات المستخدم الحالي",
    "warning" => "تحذير",
    "important_note" => "ملاحظة هامة",
    "super_admin_warning" => "يمكن فقط للمسؤولين المتميزين تعيين دور مسؤول متميز.",
    "select_role" => "اختر الدور",
    "permissions" => "صلاحية",
    "more_permissions" => "المزيد",
    "no_direct_permissions" => "لا توجد صلاحيات مباشرة معينة",
    "role_permissions" => "صلاحيات الدور",
    "leave_empty_keep_password" => "اتركه فارغاً للحفاظ على كلمة المرور الحالية.",
    "user_information" => "معلومات المستخدم",
    "view_user" => "عرض المستخدم",
    "edit_user" => "تعديل المستخدم",
    "delete_user_button" => "حذف المستخدم",
    "password_placeholder" => "أدخل 8 أحرف على الأقل",
    "password_optional" => "اختياري",

    // ============ USER VALIDATION MESSAGES ============
    "validation_name_required" => "حقل الاسم مطلوب.",
    "validation_name_max" => "الاسم لا يجب أن يتجاوز 255 حرفاً.",
    "mobile_unique" => "رقم الجوال مسجل بالفعل.",
    "validation_email_email" => "البريد الإلكتروني يجب أن يكون عنوان بريد إلكتروني صحيح.",
    "email_unique" => "البريد الإلكتروني مسجل بالفعل.",
    "password_min" => "كلمة المرور يجب أن تكون 8 أحرف على الأقل.",
    "validation_role_required" => "حقل الدور مطلوب.",
    "validation_role_exists" => "الدور المحدد غير صالح.",
    "validation_status_required" => "حقل الحالة مطلوب.",
    "validation_status_in" => "الحالة المحددة غير صالحة.",
    "validation_commission_numeric" => "معدل العمولة يجب أن يكون رقماً.",
    "validation_commission_min" => "معدل العمولة يجب أن يكون 0 على الأقل.",
    "validation_commission_max" => "معدل العمولة لا يجب أن يتجاوز 100.",

    // ============ CITY MANAGEMENT ============
    'cities_management' => 'إدارة المدن',
    'cities' => 'المدن',
    'cities_description' => 'إدارة المدن للتوصيل',
    'city_name' => 'اسم المدينة',
    'city_status' => 'حالة المدينة',
    'city_details' => 'تفاصيل المدينة',
    'cities_list' => 'قائمة المدن',

    'city_name_placeholder' => 'مثال: عمّان',
    'create_city' => 'إنشاء مدينة',
    'edit_city' => 'تعديل المدينة',
    'delete_city' => 'حذف المدينة',
    'update_city' => 'تحديث المدينة',

    'create_city_description' => 'إنشاء مدينة جديدة',
    'edit_city_description' => 'تحديث تفاصيل المدينة',

    // City Success Messages
    'city_created_successfully' => 'تم إنشاء المدينة بنجاح.',
    'city_updated_successfully' => 'تم تحديث المدينة بنجاح.',
    'city_deleted_successfully' => 'تم حذف المدينة ":name" بنجاح.',
    'city_restored_successfully' => 'تم استعادة المدينة بنجاح.',
    'city_status_updated' => 'تم تحديث حالة المدينة إلى ":status".',

    // City Empty States
    'no_cities_found' => 'لم يتم العثور على مدن',
    'no_cities_description' => 'ابدأ بإنشاء أول مدينة لك',
    'create_first_city' => 'إنشاء أول مدينة',
    'back_to_cities' => 'العودة للمدن',
    'confirm_delete_city' => 'هل أنت متأكد من حذف هذه المدينة',

    // Search & Filter Cities
    'search_cities' => 'البحث باسم المدينة',

    // City Validation Messages
    'validation_city_name_required' => 'حقل اسم المدينة مطلوب.',
    'validation_city_name_max' => 'اسم المدينة لا يجب أن يتجاوز 255 حرفاً.',
    'validation_city_name_unique' => 'اسم المدينة موجود بالفعل.',
    'validation_is_active_required' => 'حقل الحالة مطلوب.',
    'validation_is_active_boolean' => 'الحالة يجب أن تكون true أو false.',

    // ============ DELIVERY COURIER MANAGEMENT ============
    'delivery_couriers_management' => 'إدارة شركات التوصيل',
    'delivery_couriers' => 'شركات التوصيل',
    'delivery_couriers_description' => 'إدارة شركات التوصيل',

    'courier_name' => 'اسم شركة التوصيل',
    'courier_contact_phone' => 'رقم الاتصال',
    'courier_status' => 'حالة شركة التوصيل',
    'courier_details' => 'تفاصيل شركة التوصيل',
    'couriers_list' => 'قائمة شركات التوصيل',

    'courier_name_placeholder' => 'مثال: أرامكس',
    'courier_contact_phone_placeholder' => 'مثال: +962791234567',
    'create_courier' => 'إنشاء شركة توصيل',
    'edit_courier' => 'تعديل شركة التوصيل',
    'delete_courier' => 'حذف شركة التوصيل',
    'update_courier' => 'تحديث شركة التوصيل',

    'create_courier_description' => 'إنشاء شركة توصيل جديدة',
    'edit_courier_description' => 'تحديث تفاصيل شركة التوصيل',

    // Courier Success Messages
    'courier_created_successfully' => 'تم إنشاء شركة التوصيل بنجاح.',
    'courier_updated_successfully' => 'تم تحديث شركة التوصيل بنجاح.',
    'courier_deleted_successfully' => 'تم حذف شركة التوصيل ":name" بنجاح.',
    'courier_restored_successfully' => 'تم استعادة شركة التوصيل بنجاح.',
    'courier_status_updated' => 'تم تحديث حالة شركة التوصيل إلى ":status".',

    // Courier Empty States
    'no_couriers_found' => 'لم يتم العثور على شركات توصيل',
    'no_couriers_description' => 'ابدأ بإنشاء أول شركة توصيل لك',
    'create_first_courier' => 'إنشاء أول شركة توصيل',
    'back_to_couriers' => 'العودة لشركات التوصيل',
    'confirm_delete_courier' => 'هل أنت متأكد من حذف هذه الشركة',

    // Search & Filter Couriers
    'search_couriers' => 'البحث باسم شركة التوصيل',

    // Courier Validation Messages
    'validation_courier_name_required' => 'حقل اسم شركة التوصيل مطلوب.',
    'validation_courier_name_max' => 'اسم شركة التوصيل لا يجب أن يتجاوز 255 حرفاً.',
    'validation_courier_name_unique' => 'اسم شركة التوصيل موجود بالفعل.',
    'validation_contact_phone_max' => 'رقم الاتصال لا يجب أن يتجاوز 20 حرفاً.',

    // ============ DELIVERY COURIER FEE MANAGEMENT ============
    'delivery_courier_fees_management' => 'إدارة رسوم التوصيل',
    'delivery_courier_fees' => 'رسوم التوصيل',
    'delivery_courier_fees_description' => 'إدارة تسعير التوصيل لكل شركة لكل مدينة',

    'real_fee_amount' => 'سعر الرسوم الحقيقي',
    'currency' => 'العملة',
    'fee_status' => 'حالة الرسوم',
    'fee_details' => 'تفاصيل الرسوم',
    'fee_information' => 'معلومات الرسوم',
    'fees_list' => 'قائمة الرسوم',

    'create_fee' => 'إنشاء رسوم',
    'edit_fee' => 'تعديل الرسوم',
    'delete_fee' => 'حذف الرسوم',
    'update_fee' => 'تحديث الرسوم',

    'create_fee_description' => 'إنشاء رسوم توصيل جديدة',
    'edit_fee_description' => 'تحديث تفاصيل الرسوم',
    'fee_details_description' => 'عرض وإدارة تفاصيل الرسوم',

    'profit_amount' => 'مبلغ الربح',
    'profit_margin' => 'هامش الربح',

    'important_note' => 'ملاحظة هامة',
    'fee_unique_constraint_note' => 'سجل رسوم واحد فقط مسموح لكل شركة توصيل ومدينة.',

    // Fee Success Messages
    'fee_created_successfully' => 'تم إنشاء الرسوم بنجاح.',
    'fee_updated_successfully' => 'تم تحديث الرسوم بنجاح.',
    'fee_deleted_successfully' => 'تم حذف الرسوم ":info" بنجاح.',
    'fee_status_updated' => 'تم تحديث حالة الرسوم إلى ":status".',

    // Fee Empty States
    'no_fees_found' => 'لم يتم العثور على رسوم',
    'no_fees_description' => 'ابدأ بإنشاء أول رسوم لك',
    'create_first_fee' => 'إنشاء أول رسوم',
    'back_to_fees' => 'العودة للرسوم',
    'confirm_delete_fee' => 'هل أنت متأكد من حذف هذه الرسوم',

    // Search & Filter Fees
    'search_fees' => 'البحث باسم شركة التوصيل أو اسم المدينة',
    'all_couriers' => 'جميع الشركات',
    'all_cities' => 'جميع المدن',

    // Fee Validation Messages
    'validation_courier_id_required' => 'حقل شركة التوصيل مطلوب.',
    'validation_courier_id_integer' => 'شركة التوصيل يجب أن تكون عدداً صحيحاً.',
    'validation_courier_id_exists' => 'شركة التوصيل المحددة غير صالحة أو غير نشطة.',
    'validation_city_id_required' => 'حقل المدينة مطلوب.',
    'validation_city_id_integer' => 'المدينة يجب أن تكون عدداً صحيحاً.',
    'validation_city_id_exists' => 'المدينة المحددة غير صالحة أو غير نشطة.',
    'validation_real_fee_amount_required' => 'حقل سعر الرسوم الحقيقي مطلوب.',
    'validation_real_fee_amount_numeric' => 'سعر الرسوم الحقيقي يجب أن يكون رقماً.',
    'validation_real_fee_amount_decimal' => 'سعر الرسوم الحقيقي يجب أن يحتوي على 3 خانات عشرية كحد أقصى.',
    'validation_real_fee_amount_min' => 'سعر الرسوم الحقيقي يجب أن يكون 0 على الأقل.',
    'validation_real_fee_amount_max' => 'سعر الرسوم الحقيقي لا يجب أن يتجاوز 99999999.999.',
    'validation_currency_required' => 'حقل العملة مطلوب.',
    'validation_currency_max' => 'العملة لا يجب أن تتجاوز 3 أحرف.',
    'validation_courier_city_combination_unique' => 'رسوم لهذه الشركة والمدينة موجودة بالفعل.',

    // Common Labels for Courier Fees
    'select_courier' => 'اختر شركة التوصيل',
    'select_city' => 'اختر المدينة',

    // ============ DELIVERY FEES RELATIONSHIP ============
    'delivery_fees_for_city' => 'رسوم التوصيل للمدينة',
    'delivery_fees_for_courier' => 'رسوم التوصيل للشركة',
    'no_delivery_fees_found' => 'لم يتم العثور على رسوم توصيل',
    'no_delivery_fees_description' => 'لم يتم تكوين رسوم بعد.',

    // ============ ORDER MANAGEMENT ============
    'orders_management' => 'إدارة الطلبات',
    'orders_description' => 'إدارة طلبات العملاء والتوصيل',
    'order_number' => 'رقم الطلب',
    'customer_name' => 'اسم العميل',
    'customer_address' => 'عنوان التوصيل',
    'order_city' => 'المدينة',
    'order_courier' => 'شركة التوصيل',
    'order_status' => 'الحالة',
    'order_items' => 'عناصر الطلب',
    'order_details' => 'تفاصيل الطلب',
    'orders_list' => 'قائمة الطلبات',
    'order_notes' => 'ملاحظات',
    'order_phone' => 'رقم الهاتف',
    'order_phone_numbers' => 'أرقام الهواتف',
    'subtotal_products' => 'إجمالي المنتجات',
    'delivery_fee' => 'رسوم التوصيل',
    'real_delivery_fee' => 'سعر التوصيل الحقيقي',
    'coupon_discount' => 'خصم الكوبون',
    'free_delivery_discount' => 'توصيل مجاني',
    'actual_charge' => 'القيمة الفعلية',
    'total_price' => 'الإجمالي',
    'profit' => 'الربح',
    'profit_margin' => 'هامش الربح',
    'order_date' => 'تاريخ الطلب',

    // Order Statuses
    'status_processing' => 'قيد المعالجة',
    'status_with_delivery_company' => 'مع شركة التوصيل',
    'status_received' => 'مستلم',
    'status_cancelled' => 'ملغي',
    'status_returned' => 'مرتجع',
    'all_statuses' => 'جميع الحالات',

    // Order Actions
    'create_order' => 'إنشاء طلب',
    'edit_order' => 'تعديل الطلب',
    'delete_order' => 'حذف الطلب',
    'update_order' => 'تحديث الطلب',
    'view_order' => 'عرض الطلب',
    'assign_courier' => 'تعيين شركة توصيل',
    'update_status' => 'تحديث الحالة',
    'order_not_editable' => 'لا يمكن تعديل هذا الطلب',
    'order_cannot_be_deleted' => 'لا يمكن حذف هذا الطلب',

    // Order Success Messages
    'order_created_successfully' => 'تم إنشاء الطلب بنجاح.',
    'order_updated_successfully' => 'تم تحديث الطلب بنجاح.',
    'order_deleted_successfully' => 'تم حذف الطلب بنجاح.',
    'order_status_updated' => 'تم تحديث حالة الطلب إلى ":status".',
    'courier_assigned_successfully' => 'تم تعيين شركة التوصيل بنجاح.',

    // Order Empty States
    'no_orders_found' => 'لم يتم العثور على طلبات',
    'no_orders_description' => 'لم يتم تقديم طلبات بعد',
    'no_order_items_found' => 'لا توجد عناصر في هذا الطلب',

    // Search & Filter Orders
    'search_orders' => 'البحث برقم الطلب أو اسم العميل',
    'filter_by_status' => 'تصفية حسب الحالة',
    'filter_by_city' => 'تصفية حسب المدينة',
    'filter_by_courier' => 'تصفية حسب شركة التوصيل',
    'filter_by_coupon' => 'تصفية حسب الكوبون',
    'all_couriers' => 'جميع الشركات',
    'all_coupons' => 'جميع الكوبونات',

    // Order Items
    'item_product' => 'المنتج',
    'item_quantity' => 'الكمية',
    'item_base_price' => 'السعر الأساسي',
    'item_discount' => 'الخصم للوحدة',
    'item_final_price' => 'السعر النهائي',
    'item_total' => 'الإجمالي',
    'item_cost_price' => 'سعر التكلفة',
    'item_profit' => 'ربح العنصر',

    // Order Validation Messages
    'validation_order_number_required' => 'حقل رقم الطلب مطلوب.',
    'validation_full_name_required' => 'حقل اسم العميل مطلوب.',
    'validation_city_id_required' => 'حقل المدينة مطلوب.',
    'validation_address_required' => 'حقل العنوان مطلوب.',
    'validation_phone_numbers_required' => 'يجب إدخال رقم هاتف واحد على الأقل.',
    'validation_status_required' => 'حقل الحالة مطلوب.',
    'validation_status_in' => 'الحالة المحددة غير صالحة.',
    'validation_courier_id_exists' => 'شركة التوصيل المحددة غير صالحة.',
    'validation_courier_id_required' => 'حقل شركة التوصيل مطلوب.',

    // Order Status Transitions
    'status_transition_not_allowed' => 'لا يمكن الانتقال من الحالة الحالية إلى الحالة المحددة.',
    'allowed_transitions' => 'حالات الانتقال المسموحة',

    // Order Profit
    'order_profit' => 'ربح الطلب',
    'total_items' => 'إجمالي العناصر',

    // ============ COUPON MANAGEMENT ============
    'coupons_management' => 'إدارة الكوبونات',
    'coupons_description' => 'إدارة كوبونات الخصم والعروض الترويجية',
    'coupon_code' => 'رمز الكوبون',
    'coupon_type' => 'نوع الكوبون',
    'coupon_value' => 'قيمة الكوبون',
    'coupon_minimum_order' => 'الحد الأدنى للطلب',
    'coupon_max_uses' => 'الحد الأقصى للاستخدام',
    'coupon_used_count' => 'عدد مرات الاستخدام',
    'coupon_remaining_uses' => 'المرات المتبقية',
    'coupon_valid_from' => 'صالح من',
    'coupon_valid_until' => 'صالح حتى',
    'coupon_is_active' => 'الحالة',
    'coupon_details' => 'تفاصيل الكوبون',
    'coupons_list' => 'قائمة الكوبونات',

    // Coupon Types
    'type_fixed' => 'مبلغ ثابت',
    'type_percentage' => 'نسبة مئوية',
    'type_free_delivery' => 'توصيل مجاني',
    'all_types' => 'جميع الأنواع',

    // Coupon Actions
    'create_coupon' => 'إنشاء كوبون',
    'edit_coupon' => 'تعديل الكوبون',
    'delete_coupon' => 'حذف الكوبون',
    'update_coupon' => 'تحديث الكوبون',
    'view_coupon' => 'عرض الكوبون',
    'deactivate_coupon' => 'تعطيل',
    'activate_coupon' => 'تفعيل',

    // Coupon Success Messages
    'coupon_created_successfully' => 'تم إنشاء الكوبون بنجاح.',
    'coupon_updated_successfully' => 'تم تحديث الكوبون بنجاح.',
    'coupon_deleted_successfully' => 'تم حذف الكوبون بنجاح.',
    'coupon_activated' => 'تم تفعيل الكوبون بنجاح.',
    'coupon_deactivated' => 'تم تعطيل الكوبون بنجاح.',

    // Coupon Empty States
    'no_coupons_found' => 'لم يتم العثور على كوبونات',
    'no_coupons_description' => 'لم يتم إنشاء كوبونات بعد',
    'create_first_coupon' => 'إنشاء أول كوبون',

    // Search & Filter Coupons
    'search_coupons' => 'البحث برمز الكوبون',
    'filter_by_type' => 'تصفية حسب النوع',
    'filter_by_status' => 'تصفية حسب الحالة',
    'filter_active' => 'نشط',
    'filter_inactive' => 'غير نشط',

    // Coupon Validation Messages
    'validation_code_required' => 'حقل رمز الكوبون مطلوب.',
    'validation_code_unique' => 'رمز الكوبون موجود بالفعل.',
    'validation_code_max' => 'رمز الكوبون يجب ألا يتجاوز 50 حرفاً.',
    'validation_type_required' => 'حقل نوع الكوبون مطلوب.',
    'validation_type_in' => 'النوع المحدد غير صالح.',
    'validation_value_required' => 'حقل قيمة الكوبون مطلوب.',
    'validation_value_numeric' => 'القيمة يجب أن تكون رقماً.',
    'validation_value_min' => 'القيمة يجب أن تكون 0 على الأقل.',
    'validation_value_max_percentage' => 'القيمة المئوية يجب ألا تتجاوز 100.',
    'validation_minimum_order_numeric' => 'الحد الأدنى للطلب يجب أن يكون رقماً.',
    'validation_minimum_order_min' => 'الحد الأدنى للطلب يجب أن يكون 0 على الأقل.',
    'validation_max_uses_integer' => 'الحد الأقصى للاستخدام يجب أن يكون عدداً صحيحاً.',
    'validation_max_uses_min' => 'الحد الأقصى للاستخدام يجب أن يكون 1 على الأقل.',
    'validation_valid_from_required' => 'تاريخ البدء مطلوب.',
    'validation_valid_from_date' => 'تاريخ البدء يجب أن يكون تاريخاً صالحاً.',
    'validation_valid_until_after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء.',
    'validation_code_format' => 'رمز الكوبون يمكن أن يحتوي على أحرف وأرقام وشرطات سفلية فقط.',

    // Coupon Help Text
    'coupon_code_help' => 'رمز فريد سيدخله العملاء (مثال: SUMMER2024)',
    'coupon_type_help' => 'اختر كيفية تطبيق الخصم',
    'coupon_value_help' => 'مبلغ ثابت (دينار) أو نسبة مئوية (0-100)',
    'coupon_minimum_order_help' => 'الحد الأدنى لإجمالي السلة لاستخدام هذا الكوبون',
    'coupon_max_uses_help' => 'اتركه فارغاً للاستخدام غير المحدود',
    'coupon_valid_from_help' => 'متى يبدأ الكوبون في العمل',
    'coupon_valid_until_help' => 'اتركه فارغاً لعدم انتهاء الصلاحية',

    // Coupon Usage Info
    'coupon_usage_info' => 'معلومات الاستخدام',
    'coupon_times_used' => 'مرات الاستخدام',
    'coupon_orders_count' => 'الطلبات المستخدمة لهذا الكوبون',
    'coupon_discount_given' => 'إجمالي الخصم الممنوح',

    // Coupon Status
    'status_active' => 'نشط',
    'status_inactive' => 'غير نشط',
    'coupon_is_expired' => 'منتهي الصلاحية',
    'coupon_not_started' => 'لم يبدأ بعد',
    'coupon_unlimited' => 'غير محدود',

    // Confirmation Messages
    'confirm_delete_coupon' => 'هل أنت متأكد من حذف هذا الكوبون؟',
    'confirm_deactivate_coupon' => 'هل أنت متأكد من تعطيل هذا الكوبون؟',

    // Coupon Affects
    'coupon_applies_to' => 'يطبق على',
    'applies_to_products' => 'المنتجات',
    'applies_to_delivery' => 'التوصيل',

    // Additional Help Text
    'is_active_help' => 'ما إذا كان هذا الكوبون متاحاً حالياً للعملاء',
    'coupon_already_used' => 'تم استخدام هذا الكوبون :count مرة. تعديله سيؤثر على الطلبات الحالية.',
    'create_coupon_description' => 'إنشاء كوبون خصم جديد للعملاء',
    'edit_coupon_description' => 'تحديث تفاصيل وتقييدات الكوبون',
    'cannot_delete_used_coupon' => 'لا يمكن حذف الكوبون الذي تم استخدامه في :count طلب/طلبات.',

    // Validation for minimum_order_amount
    'validation_minimum_order_required' => 'حقل الحد الأدنى للطلب مطلوب.',

    // Additional Help Text
    'important_note' => 'ملاحظة هامة',
    'warning' => 'تحذير',

    // ============ DELETE MODAL ============
    'confirm_delete' => 'تأكيد الحذف',
    'confirm_delete_message' => 'هل أنت متأكد من حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.',
    'confirm_delete_item' => 'هل أنت متأكد من حذف ":item"؟',
];
