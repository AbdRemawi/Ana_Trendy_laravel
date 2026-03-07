# Products API Filtering - Nuxt 3 Implementation Guide

## Table of Contents
1. [API Overview](#api-overview)
2. [Filter Parameters Reference](#filter-parameters-reference)
3. [Dynamic Filter Metadata](#dynamic-filter-metadata)
4. [Nuxt 3 Implementation](#nuxt-3-implementation)
5. [Advanced Filter Patterns](#advanced-filter-patterns)

---

## API Overview

### Base Endpoint
```
GET /api/v1/products
```

### Response Structure
```typescript
interface ProductsResponse {
  data: Product[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    filters: FilterMetadata
  }
}

interface FilterMetadata {
  brands: { id: number, name: string, slug: string }[]
  price_range: { min: number, max: number }
  sizes: ('S' | 'M' | 'L' | 'XL' | 'XXL')[]
  genders: ('male' | 'female' | 'unisex')[]
  has_offers: boolean
  offer_count: number
  total_products: number
}
```

---

## Filter Parameters Reference

### 1. Price Range Filter
Filters products by effective price (offer_price if available, else sale_price).

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `min_price` | number | Minimum effective price | `?min_price=50` |
| `max_price` | number | Maximum effective price | `?max_price=300` |

**Combined:**
```
GET /api/v1/products?min_price=50&max_price=300
```

### 2. Brand Filter
Multiple ways to filter by brand(s).

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `brand_id` | number | Single brand by ID | `?brand_id=1` |
| `brand_ids[]` | number[] | Multiple brands by ID | `?brand_ids[]=1&brand_ids[]=2` |
| `brand` | string | Single brand by slug | `?brand=nike` |
| `brands[]` | string[] | Multiple brands by slug | `?brands[]=nike&brands[]=adidas` |

**Multiple Brands by ID (Recommended for Backend):**
```
GET /api/v1/products?brand_ids[]=1&brand_ids[]=2&brand_ids[]=3
```

**Multiple Brands by Slug (Recommended for Frontend):**
```
GET /api/v1/products?brands[]=nike&brands[]=adidas&brands[]=puma
```

### 3. Offer Filter (Boolean)
Filter products that have active offers.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `offers_only` | boolean | Show only products with offers | `?offers_only=1` |

**Important:** An offer is considered active when:
- `offer_price` is NOT NULL
- `offer_price < sale_price`

```
GET /api/v1/products?offers_only=1
```

### 4. Size Filter
Filter by product size.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `size` | string | Product size | `?size=M` |

```
GET /api/v1/products?size=M
```

### 5. Category Filter
Filter by category.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `category_id` | number | Category by ID | `?category_id=5` |
| `category` | string | Category by slug | `?category=shoes` |

### 6. Gender Filter
Filter by target gender.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `gender` | string | Target gender | `?gender=male` |

### 7. Sorting
Sort products by various criteria.

| Parameter | Values | Description |
|-----------|--------|-------------|
| `sort` | `price_low_high`, `price_high_low`, `newest` | Sort order |

```
GET /api/v1/products?sort=price_low_high
```

### 8. Pagination
Control page size and page number.

| Parameter | Type | Default | Limits |
|-----------|------|---------|--------|
| `per_page` | number | 15 | 1-100 |
| `page` | number | 1 | min: 1 |

---

## Dynamic Filter Metadata

### Key Concept: Filters Affect Available Options

**The `filters` object in the response is dynamic** - it changes based on your current filter selections.

**Example:**
1. Initial request returns all brands with price range $10-$500
2. Select "Nike" brand → response shows price range $50-$300 (only Nike products)
3. Select size "L" → response shows price range $80-$250 (only Nike + L size)

This enables "smart" filtering where users only see options that exist in the current filtered result set.

### Metadata Update Flow

```
User Action                → API Request                          → Response
─────────────────────────────────────────────────────────────────────────────
Initial load               → GET /api/v1/products                  → All brands, all sizes
Select Brand "Nike"         → GET /api/v1/products?brands[]=nike   → Nike products only
Select Size "M"            → GET /api/v1/products?brands[]=nike&size=M → Nike + M only
Set Price Range $100-$200  → GET /api/v1/products?brands[]=nike&size=M&min_price=100&max_price=200
```

Each response contains updated `meta.filters` reflecting available options.

---

## Nuxt 3 Implementation

### 1. Products Composable

Create `composables/useProducts.ts`:

```typescript
// composables/useProducts.ts
import { ref, computed, watch, watchEffect } from 'vue'

interface ProductFilters {
  brands?: string[]        // Array of brand slugs
  category?: string        // Category slug
  size?: string            // Single size
  gender?: string          // Single gender
  minPrice?: number        // Minimum price
  maxPrice?: number        // Maximum price
  offersOnly?: boolean     // Show only offers
  sort?: string            // Sort option
  perPage?: number         // Items per page
  page?: number            // Page number
}

interface ProductsState {
  products: Product[]
  metadata: FilterMetadata | null
  loading: boolean
  error: string | null
}

export const useProducts = () => {
  const filters = useState<ProductFilters>('product-filters', () => ({}))
  const state = useState<ProductsState>('products-state', () => ({
    products: [],
    metadata: null,
    loading: false,
    error: null,
  }))

  /**
   * Build query string from filters object
   */
  const buildQueryString = (filters: ProductFilters): string => {
    const params = new URLSearchParams()

    // Multiple brands (array)
    if (filters.brands?.length) {
      filters.brands.forEach(brand => params.append('brands[]', brand))
    }

    // Category (single)
    if (filters.category) {
      params.append('category', filters.category)
    }

    // Size (single)
    if (filters.size) {
      params.append('size', filters.size)
    }

    // Gender (single)
    if (filters.gender) {
      params.append('gender', filters.gender)
    }

    // Price range
    if (filters.minPrice !== undefined) {
      params.append('min_price', filters.minPrice.toString())
    }
    if (filters.maxPrice !== undefined) {
      params.append('max_price', filters.maxPrice.toString())
    }

    // Offers only (boolean)
    if (filters.offersOnly) {
      params.append('offers_only', '1')
    }

    // Sort
    if (filters.sort) {
      params.append('sort', filters.sort)
    }

    // Pagination
    if (filters.perPage) {
      params.append('per_page', filters.perPage.toString())
    }
    if (filters.page) {
      params.append('page', filters.page.toString())
    }

    return params.toString()
  }

  /**
   * Fetch products with current filters
   */
  const fetchProducts = async () => {
    state.value.loading = true
    state.value.error = null

    try {
      const queryString = buildQueryString(filters.value)
      const { data, error } = await useFetch<{ data: Product[], meta: any }>(
        `/api/v1/products${queryString ? '?' + queryString : ''}`
      )

      if (error.value) {
        throw error.value
      }

      state.value.products = data.value.data
      state.value.metadata = data.value.meta.filters
    } catch (e: any) {
      state.value.error = e.message
    } finally {
      state.value.loading = false
    }
  }

  /**
   * Update filters and refetch
   */
  const updateFilters = (newFilters: Partial<ProductFilters>) => {
    filters.value = { ...filters.value, ...newFilters }
    // Reset to page 1 when filters change
    filters.value.page = 1
  }

  /**
   * Toggle brand selection (multi-select)
   */
  const toggleBrand = (brandSlug: string) => {
    const brands = filters.value.brands || []

    if (brands.includes(brandSlug)) {
      // Remove brand
      filters.value.brands = brands.filter(b => b !== brandSlug)
    } else {
      // Add brand
      filters.value.brands = [...brands, brandSlug]
    }

    // Reset to empty array if no brands selected
    if (filters.value.brands!.length === 0) {
      delete filters.value.brands
    }
  }

  /**
   * Clear all filters
   */
  const clearFilters = () => {
    filters.value = {}
  }

  /**
   * Clear a specific filter
   */
  const clearFilter = (filterKey: keyof ProductFilters) => {
    delete filters.value[filterKey]
  }

  /**
   * Check if any filters are active
   */
  const hasActiveFilters = computed(() => {
    return Object.keys(filters.value).length > 0
  })

  /**
   * Get count of active filters
   */
  const activeFiltersCount = computed(() => {
    let count = 0
    if (filters.value.brands?.length) count++
    if (filters.value.size) count++
    if (filters.value.gender) count++
    if (filters.value.minPrice !== undefined || filters.value.maxPrice !== undefined) count++
    if (filters.value.offersOnly) count++
    if (filters.value.category) count++
    return count
  })

  // Watch for filter changes and refetch
  watch(
    () => filters.value,
    () => {
      fetchProducts()
    },
    { deep: true }
  )

  return {
    // State
    products: computed(() => state.value.products),
    metadata: computed(() => state.value.metadata),
    loading: computed(() => state.value.loading),
    error: computed(() => state.value.error),

    // Computed
    hasActiveFilters,
    activeFiltersCount,

    // Methods
    fetchProducts,
    updateFilters,
    toggleBrand,
    clearFilters,
    clearFilter,
  }
}
```

### 2. Products Page Component

Create `pages/products/index.vue`:

```vue
<script setup lang="ts">
import { useProducts } from '~/composables/useProducts'

const {
  products,
  metadata,
  loading,
  error,
  hasActiveFilters,
  activeFiltersCount,
  updateFilters,
  toggleBrand,
  clearFilters,
  clearFilter,
} = useProducts()

// Initialize with default values on mount
onMounted(() => {
  updateFilters({
    perPage: 20,
    sort: 'newest',
  })
})

// Price range state (two-way bound with range slider)
const priceRange = ref<{ min: number; max: number }>({
  min: 0,
  max: 1000,
})

// Watch for price range changes with debounce
const debouncedPriceUpdate = useDebounceFn(() => {
  updateFilters({
    minPrice: priceRange.value.min,
    maxPrice: priceRange.value.max,
  })
}, 500)

watch(priceRange, debouncedPriceUpdate, { deep: true })

// Update price range when metadata changes
watchEffect(() => {
  if (metadata.value?.price_range) {
    priceRange.value = {
      min: metadata.value.price_range.min,
      max: metadata.value.price_range.max,
    }
  }
})
</script>

<template>
  <div class="products-page">
    <!-- Filter Sidebar -->
    <aside class="filter-sidebar">
      <!-- Active Filters Summary -->
      <div v-if="hasActiveFilters" class="active-filters">
        <h3>Active Filters ({{ activeFiltersCount }})</h3>
        <button @click="clearFilters">Clear All</button>
      </div>

      <!-- Brand Filter (Multi-Select) -->
      <FilterSection title="Brands">
        <div class="brand-list">
          <label
            v-for="brand in metadata?.brands"
            :key="brand.id"
            class="brand-checkbox"
          >
            <input
              type="checkbox"
              :value="brand.slug"
              :checked="filters.brands?.includes(brand.slug)"
              @change="toggleBrand(brand.slug)"
            />
            <span>{{ brand.name }}</span>
          </label>
        </div>
      </FilterSection>

      <!-- Price Range Filter -->
      <FilterSection title="Price Range">
        <div class="price-inputs">
          <div class="price-input">
            <label>Min</label>
            <input
              v-model.number="priceRange.min"
              type="number"
              :min="metadata?.price_range?.min || 0"
              :max="priceRange.max"
            />
          </div>
          <div class="price-input">
            <label>Max</label>
            <input
              v-model.number="priceRange.max"
              type="number"
              :min="priceRange.min"
              :max="metadata?.price_range?.max || 1000"
            />
          </div>
        </div>

        <!-- Range Slider -->
        <input
          v-model.number="priceRange.min"
          type="range"
          :min="metadata?.price_range?.min || 0"
          :max="priceRange.max"
          class="range-slider"
        />
        <input
          v-model.number="priceRange.max"
          type="range"
          :min="priceRange.min"
          :max="metadata?.price_range?.max || 1000"
          class="range-slider"
        />
      </FilterSection>

      <!-- Size Filter (Single Select) -->
      <FilterSection title="Size">
        <div class="size-options">
          <button
            v-for="size in metadata?.sizes"
            :key="size"
            :class="['size-btn', { active: filters.size === size }]"
            @click="updateFilters({ size: filters.size === size ? undefined : size })"
          >
            {{ size }}
          </button>
        </div>
      </FilterSection>

      <!-- Gender Filter (Single Select) -->
      <FilterSection title="Gender">
        <div class="gender-options">
          <label
            v-for="gender in metadata?.genders"
            :key="gender"
            class="gender-radio"
          >
            <input
              type="radio"
              :value="gender"
              :checked="filters.gender === gender"
              @change="updateFilters({ gender })"
            />
            <span class="capitalize">{{ gender }}</span>
          </label>
        </div>
      </FilterSection>

      <!-- Offers Filter (Boolean Toggle) -->
      <FilterSection title="Special Offers">
        <label class="toggle-switch">
          <input
            type="checkbox"
            :checked="filters.offersOnly"
            @change="updateFilters({ offersOnly: !filters.offersOnly ? true : undefined })"
          />
          <span>Show only products with offers</span>
          <span v-if="metadata?.offer_count" class="offer-badge">
            {{ metadata.offer_count }} offers
          </span>
        </label>
      </FilterSection>
    </aside>

    <!-- Products Grid -->
    <main class="products-content">
      <!-- Sorting -->
      <div class="toolbar">
        <select
          :value="filters.sort"
          @change="updateFilters({ sort: $event.target.value })"
        >
          <option value="newest">Newest First</option>
          <option value="price_low_high">Price: Low to High</option>
          <option value="price_high_low">Price: High to Low</option>
        </select>

        <div class="results-count">
          {{ metadata?.total_products }} products found
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading">
        <Spinner />
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error">
        {{ error }}
      </div>

      <!-- Products Grid -->
      <div v-else class="products-grid">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- No Results -->
      <div v-if="!loading && products.length === 0" class="no-results">
        <p>No products found matching your filters.</p>
        <button @click="clearFilters">Clear Filters</button>
      </div>
    </main>
  </div>
</template>

<style scoped>
.products-page {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
}

.filter-sidebar {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.brand-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.brand-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.price-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.price-input input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.size-options {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.size-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
}

.size-btn.active {
  background: #333;
  color: white;
  border-color: #333;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.capitalize {
  text-transform: capitalize;
}

.offer-badge {
  background: #e53e3e;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 999px;
  font-size: 0.75rem;
}
</style>
```

### 3. Filter Section Component

Create `components/FilterSection.vue`:

```vue
<script setup lang="ts">
interface Props {
  title: string
}

defineProps<Props>()
</script>

<template>
  <div class="filter-section">
    <h3>{{ title }}</h3>
    <div class="filter-content">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.filter-section {
  padding: 1.5rem 0;
  border-bottom: 1px solid #eee;
}

.filter-section h3 {
  margin: 0 0 1rem 0;
  font-size: 1rem;
  font-weight: 600;
}
</style>
```

---

## Advanced Filter Patterns

### 1. URL Synchronization (SSR-Friendly)

Sync filters with URL query params for shareable links and SSR:

```typescript
// composables/useProducts.ts
export const useProducts = () => {
  const route = useRoute()
  const router = useRouter()

  // Initialize filters from URL on mount
  onMounted(() => {
    const query = route.query

    filters.value = {
      brands: query.brands ? (Array.isArray(query.brands) ? query.brands : [query.brands]) : undefined,
      category: query.category as string || undefined,
      size: query.size as string || undefined,
      gender: query.gender as string || undefined,
      minPrice: query.min_price ? Number(query.min_price) : undefined,
      maxPrice: query.max_price ? Number(query.max_price) : undefined,
      offersOnly: query.offers_only === '1' || query.offers_only === 'true',
      sort: query.sort as string || undefined,
      page: query.page ? Number(query.page) : 1,
      perPage: query.per_page ? Number(query.per_page) : 20,
    }

    fetchProducts()
  })

  // Update URL when filters change
  watch(
    () => filters.value,
    () => {
      const query: Record<string, string | string[]> = {}

      if (filters.value.brands?.length) query.brands = filters.value.brands
      if (filters.value.category) query.category = filters.value.category
      if (filters.value.size) query.size = filters.value.size
      if (filters.value.gender) query.gender = filters.value.gender
      if (filters.value.minPrice !== undefined) query.min_price = filters.value.minPrice.toString()
      if (filters.value.maxPrice !== undefined) query.max_price = filters.value.maxPrice.toString()
      if (filters.value.offersOnly) query.offers_only = '1'
      if (filters.value.sort) query.sort = filters.value.sort
      if (filters.value.page && filters.value.page > 1) query.page = filters.value.page.toString()
      if (filters.value.perPage && filters.value.perPage !== 20) query.per_page = filters.value.perPage.toString()

      router.replace({ query })
    },
    { deep: true }
  )

  // ... rest of the composable
}
```

### 2. Preset Filter Combinations

Create common filter presets:

```typescript
// composables/useFilterPresets.ts
export const useFilterPresets = () => {
  const { updateFilters, clearFilters } = useProducts()

  const presets = {
    sale: () => {
      clearFilters()
      updateFilters({ offersOnly: true })
    },
    newArrivals: () => {
      clearFilters()
      updateFilters({ sort: 'newest' })
    },
    under100: () => {
      clearFilters()
      updateFilters({ maxPrice: 100 })
    },
    nikeMen: () => {
      clearFilters()
      updateFilters({
        brands: ['nike'],
        gender: 'male'
      })
    }
  }

  return { presets }
}
```

### 3. Filter Persistence (localStorage)

Persist user's filter preferences:

```typescript
// composables/useProducts.ts
export const useProducts = () => {
  const FILTER_STORAGE_KEY = 'product-filters'

  // Load saved filters
  const loadSavedFilters = (): ProductFilters => {
    if (process.client) {
      const saved = localStorage.getItem(FILTER_STORAGE_KEY)
      return saved ? JSON.parse(saved) : {}
    }
    return {}
  }

  // Save filters when changed
  watch(
    () => filters.value,
    () => {
      if (process.client) {
        localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(filters.value))
      }
    },
    { deep: true }
  )

  // ... rest of the composable
}
```

### 4. Reactive Price Range with Metadata

Auto-update price range based on filtered results:

```vue
<script setup lang="ts">
const { metadata, updateFilters } = useProducts()

// Price range state
const minPrice = ref<number>(0)
const maxPrice = ref<number>(1000)

// Update local state when metadata changes (filtered results)
watchEffect(() => {
  if (metadata.value?.price_range) {
    // Only update if current range is outside metadata range
    if (minPrice.value < metadata.value.price_range.min) {
      minPrice.value = metadata.value.price_range.min
    }
    if (maxPrice.value > metadata.value.price_range.max) {
      maxPrice.value = metadata.value.price_range.max
    }
  }
})

// Apply filter when user changes values
const applyPriceFilter = useDebounceFn(() => {
  updateFilters({
    minPrice: minPrice.value,
    maxPrice: maxPrice.value
  })
}, 500)
</script>
```

---

## API Call Examples

### Example 1: Multiple Brands + Price Range
```http
GET /api/v1/products?brands[]=nike&brands[]=adidas&min_price=50&max_price=200
```

### Example 2: Offers Only + Size + Gender
```http
GET /api/v1/products?offers_only=1&size=M&gender=female
```

### Example 3: Category + Sort + Pagination
```http
GET /api/v1/products?category=shoes&sort=price_low_high&page=2&per_page=30
```

### Example 4: All Filters Combined
```http
GET /api/v1/products?
    brands[]=nike&brands[]=adidas
    &category=sneakers
    &size=M
    &gender=male
    &min_price=80
    &max_price=250
    &offers_only=1
    &sort=price_low_high
    &page=1
    &per_page=20
```

---

## Summary

| Filter | Type | Multi-Select | Updates Metadata |
|--------|------|--------------|------------------|
| Brands | slug/ID | ✅ Yes | ✅ Yes |
| Price Range | min/max | ✅ Range | ✅ Yes |
| Size | enum | ❌ Single | ✅ Yes |
| Gender | enum | ❌ Single | ✅ Yes |
| Offers | boolean | ❌ Toggle | ✅ Yes |
| Category | slug/ID | ❌ Single | ✅ Yes |

**Key Takeaway:** The filter metadata is **dynamic and context-aware**. Each filter selection updates the available options in the metadata, enabling a smart, user-friendly filtering experience.
