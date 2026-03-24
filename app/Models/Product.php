<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, HasStatus, Searchable, SoftDeletes;

    /**
     * Product size constants
     */
    public const SIZE_S = 'S';
    public const SIZE_M = 'M';
    public const SIZE_L = 'L';
    public const SIZE_XL = 'XL';
    public const SIZE_XXL = 'XXL';

    /**
     * Product gender constants
     */
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_UNISEX = 'unisex';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sku',
        'brand_id',
        'category_id',
        'name',
        'slug',
        'description',
        'size',
        'gender',
        'cost_price',
        'sale_price',
        'offer_price',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'effective_price',
        'has_offer',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'brand_id' => 'integer',
            'category_id' => 'integer',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true)->limit(1);
    }

    /**
     * Get the inventory transactions for the product.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)->latest();
    }

    /**
     * Get the current stock quantity for the product.
     * Calculated as: supply + return - sale - damage +/- adjustment
     * Uses optimized single-query calculation instead of loading all records.
     */
    public function getStockQuantityAttribute(): int
    {
        return (int) $this->inventoryTransactions()
            ->selectRaw('SUM(CASE
                WHEN type IN ("supply", "return") THEN quantity
                WHEN type IN ("sale", "damage") THEN -quantity
                WHEN type = "adjustment" THEN quantity
                ELSE 0
            END) as total')
            ->value('total') ?? 0;
    }

    /**
     * Calculate the projected stock after a transaction.
     * Returns null if product doesn't exist yet.
     *
     * @param string $type Transaction type
     * @param int $quantity Transaction quantity
     * @return int|null Projected stock or null if new product
     */
    public function calculateProjectedStock(string $type, int $quantity): ?int
    {
        $currentStock = $this->stock_quantity;

        $projectedChange = match ($type) {
            'supply', 'return' => $quantity,
            'sale', 'damage' => -$quantity,
            'adjustment' => $quantity,
            default => 0,
        };

        return $currentStock + $projectedChange;
    }

    /**
     * Validate that a transaction won't result in negative stock.
     *
     * @param string $type Transaction type
     * @param int $quantity Transaction quantity
     * @return bool True if transaction is safe (stock >= 0)
     */
    public function canApplyTransaction(string $type, int $quantity): bool
    {
        $projectedStock = $this->calculateProjectedStock($type, $quantity);

        return $projectedStock !== null && $projectedStock >= 0;
    }

    /**
     * Boot the model.
     * Auto-generate slug from name before saving.
     */
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = self::generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = self::generateUniqueSlug($product->name);
            }
        });
    }

    /**
     * Generate a unique slug from the given name.
     */
    protected static function generateUniqueSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Check if product has an active offer.
     */
    public function hasOffer(): bool
    {
        return $this->offer_price !== null && $this->offer_price < $this->sale_price;
    }

    /**
     * Get the has_offer attribute for JSON serialization.
     */
    protected function getHasOfferAttribute(): bool
    {
        return $this->hasOffer();
    }

    /**
     * Get the effective price (offer price if available, otherwise sale price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->hasOffer() ? $this->offer_price : $this->sale_price;
    }

    /**
     * Get the profit margin percentage.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }

        $effectivePrice = $this->getEffectivePriceAttribute();
        $profit = $effectivePrice - $this->cost_price;

        return round(($profit / $this->cost_price) * 100, 2);
    }

    /**
     * Scope to filter by size.
     */
    public function scopeBySize(Builder $query, string $size): Builder
    {
        return $query->where('size', $size);
    }

    /**
     * Scope to filter by gender.
     */
    public function scopeByGender(Builder $query, string $gender): Builder
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope to filter by brand.
     */
    public function scopeByBrand(Builder $query, int $brandId): Builder
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get products with offers.
     */
    public function scopeWithOffers(Builder $query): Builder
    {
        return $query->whereNotNull('offer_price')
            ->whereColumn('offer_price', '<', 'sale_price');
    }

    /**
     * Scope to include stock quantity in query (optimized).
     */
    public function scopeWithStockQuantity(Builder $query): Builder
    {
        return $query->withCount([
            'inventoryTransactions as stock_quantity' => function ($query) {
                $query->select(\DB::raw('SUM(CASE
                    WHEN type IN ("supply", "return") THEN quantity
                    WHEN type IN ("sale", "damage") THEN -quantity
                    WHEN type = "adjustment" THEN quantity
                    ELSE 0
                END)'));
            }
        ]);
    }

    /**
     * Scope to get products that are in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereExists(function ($q) {
            $q->select(\DB::raw(1))
                ->from('inventory_transactions')
                ->whereColumn('inventory_transactions.product_id', 'products.id')
                ->groupBy('inventory_transactions.product_id')
                ->havingRaw('SUM(CASE
                    WHEN type IN ("supply", "return") THEN quantity
                    WHEN type IN ("sale", "damage") THEN -quantity
                    WHEN type = "adjustment" THEN quantity
                    ELSE 0
                END) > 0');
        });
    }

    /**
     * Get available sizes as an array with translated labels.
     *
     * @return array<string, string>
     */
    public static function getAvailableSizes(): array
    {
        return [
            self::SIZE_S => __('admin.size_s'),
            self::SIZE_M => __('admin.size_m'),
            self::SIZE_L => __('admin.size_l'),
            self::SIZE_XL => __('admin.size_xl'),
            self::SIZE_XXL => __('admin.size_xxl'),
        ];
    }

    /**
     * Get available size values only (for validation).
     *
     * @return array<string>
     */
    public static function getSizeValues(): array
    {
        return [
            self::SIZE_S,
            self::SIZE_M,
            self::SIZE_L,
            self::SIZE_XL,
            self::SIZE_XXL,
        ];
    }

    /**
     * Get available genders as an array.
     */
    public static function getAvailableGenders(): array
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE,
            self::GENDER_UNISEX,
        ];
    }
}
