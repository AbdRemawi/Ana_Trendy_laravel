# Ana Trendy - Laravel E-commerce Application

A Laravel-based e-commerce platform with advanced product filtering, role-based permissions, inventory management, and order processing.

## Features

- **Authentication & Authorization**
  - Role-based access control (Admin, Manager, User)
  - Permission-based access management
  - Multi-language support (Arabic/English)

- **Product Management**
  - Advanced product filtering and search
  - Image management with primary image selection
  - Size and gender categorization
  - Offer/discount pricing support
  - Stock quantity tracking

- **Inventory Management**
  - Transaction-based inventory tracking
  - Supply, sale, return, damage, and adjustment transactions
  - Real-time stock calculation
  - Stock validation before transactions

- **Order Management**
  - Courier assignment
  - City-based delivery fees
  - Coupon code system
  - Order status workflow

## Product Filtering System

### Overview

The application implements a robust product filtering system that works across both the admin panel and public API. The filtering system is designed to be:

- **Performant**: Uses database queries and indexes for fast filtering
- **Flexible**: Supports multiple filter combinations
- **Reusable**: Built on traits that can be applied to any model
- **Dynamic**: Returns available filter options based on current results

### Filterable Trait

The `Filterable` trait (`app/Traits/Filterable.php`) provides reusable query filtering scopes:

```php
trait Filterable
{
    // Filter by status
    public function scopeByStatus(Builder $query, ?string $status): Builder;

    // Apply common filters from request
    public function scopeApplyFilters(
        Builder $query,
        Request $request,
        array $filters = [],
        array $searchColumns = ['name']
    ): Builder;

    // Paginate filtered results
    public function scopePaginateFiltered(
        Builder $query,
        Request $request,
        int $perPage = 25
    ): LengthAwarePaginator;
}
```

### Searchable Trait

The `Searchable` trait (`app/Traits/Searchable.php`) provides search functionality:

```php
trait Searchable
{
    // Search with OR logic (matches any column)
    public function scopeSearch(
        Builder $query,
        ?string $search,
        array $columns = ['name']
    ): Builder;

    // Search with AND logic (matches all columns)
    public function scopeSearchAll(
        Builder $query,
        ?string $search,
        array $columns = ['name']
    ): Builder;
}
```

### Admin Panel Filtering

**Location**: `app/Http/Controllers/Admin/ProductController.php`

The admin product index supports the following filters:

| Filter | Description | Example |
|--------|-------------|---------|
| `brand` | Filter by brand ID | `?brand=5` |
| `category` | Filter by category ID | `?category=3` |
| `size` | Filter by product size | `?size=L` |
| `gender` | Filter by gender | `?gender=female` |
| `status` | Filter by status | `?status=active` |
| `search` | Search in product name | `?search=bag` |

**How it works in the controller:**

```php
public function index(Request $request): View
{
    $query = Product::query()
        ->with(['brand', 'category', 'images'])
        ->withStockQuantity();

    // Apply filters
    if ($request->filled('brand')) {
        $query->byBrand($request->brand);
    }

    if ($request->filled('category')) {
        $query->byCategory($request->category);
    }

    if ($request->filled('size')) {
        $query->bySize($request->size);
    }

    if ($request->filled('gender')) {
        $query->byGender($request->gender);
    }

    if ($request->filled('status')) {
        $query->byStatus($request->status);
    }

    if ($request->filled('search')) {
        $query->search($request->search);
    }

    $products = $query->latest()->paginate(20);
}
```

### API Filtering

**Location**: `app/Http/Controllers/Api/ProductController.php`

The public API provides advanced filtering with metadata:

#### Available Filters

| Filter | Type | Description | Example |
|--------|------|-------------|---------|
| `brand` | string | Filter by brand slug | `?brand=gucci` |
| `brands` | array | Filter by multiple brand slugs | `?brands[]=gucci&brands[]=prada` |
| `brand_id` | int | Filter by brand ID | `?brand_id=5` |
| `brand_ids` | array | Filter by multiple brand IDs | `?brand_ids[]=5&brand_ids[]=7` |
| `category` | string | Filter by category slug | `?category=handbags` |
| `category_id` | int | Filter by category ID | `?category_id=3` |
| `size` | string | Filter by size | `?size=L` |
| `gender` | string | Filter by gender | `?gender=female` |
| `min_price` | float | Minimum effective price | `?min_price=50` |
| `max_price` | float | Maximum effective price | `?max_price=500` |
| `offers_only` | boolean | Only products with offers | `?offers_only=1` |
| `search` | string | Search in product name | `?search=leather` |

#### Sorting Options

| Value | Description |
|-------|-------------|
| `newest` | Newest products first (default) |
| `price_low_high` | Price: low to high |
| `price_high_low` | Price: high to low |

**Example API Request:**

```bash
GET /api/products?brand=gucci&size=L&min_price=100&max_price=500&sort=price_low_high
```

#### Response with Filter Metadata

The API returns metadata about available filters based on the current results:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 42,
    "filters": {
      "brands": [
        {"id": 5, "name": "Gucci", "slug": "gucci"},
        {"id": 7, "name": "Prada", "slug": "prada"}
      ],
      "price_range": {
        "min": 100,
        "max": 500
      },
      "sizes": ["S", "M", "L", "XL"],
      "genders": ["female", "unisex"],
      "has_offers": true,
      "offer_count": 12,
      "total_products": 42
    }
  }
}
```

### Product Model Scopes

**Location**: `app/Models/Product.php`

The Product model includes these filtering scopes:

```php
// Filter by brand
Product::byBrand($brandId);

// Filter by category
Product::byCategory($categoryId);

// Filter by size
Product::bySize('L');

// Filter by gender
Product::byGender('female');

// Filter by status
Product::byStatus('active');

// Only products with active offers
Product::withOffers();

// Include stock quantity in query
Product::withStockQuantity();

// Only products in stock
Product::inStock();

// Search in name
Product::search('leather bag');
```

### Filter Effectiveness

The filtering system improves the user experience by:

1. **Narrowing Results**: Users can quickly find products matching specific criteria
2. **Price Range Control**: Set budget limits with min/max price filters
3. **Brand/Category Browsing**: Filter by favorite brands or categories
4. **Attribute Selection**: Find products by size (S, M, L, XL, XXL) and gender (male, female, unisex)
5. **Deal Hunting**: Use `offers_only` to find discounted products
6. **Smart Metadata**: API returns available filter options based on current filtered results

### Performance Optimizations

- **Database Indexes**: Filtered columns have indexes for fast queries
- **Single Query Stock Calculation**: Stock quantity calculated in one query using `withStockQuantity()`
- **Eager Loading**: Related data (brand, category, images) loaded efficiently
- **Metadata Cloning**: Filter metadata calculated without affecting main query

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `npm install && npm run dev`
7. Start the development server: `php artisan serve`

## Default Credentials

After seeding, you can login with:
- **Email**: admin@example.com
- **Password**: password

## Testing

Run the test suite:
```bash
php artisan test
```

## License

This project is open-sourced software licensed under the MIT license.
