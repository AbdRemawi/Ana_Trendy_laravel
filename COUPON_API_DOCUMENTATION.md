# Coupon API Documentation

## Overview

The Coupon API provides endpoints to validate coupon codes and calculate discounts for orders. These endpoints allow the frontend to show real-time discount information to users before they complete their purchase.

**Base URL:** `http://localhost/api/v1`

---

## ⚠️ Important: Coupons & Discounted Products

**Coupons do NOT apply to products that already have a discount (an active offer price).**

- `fixed` and `percentage` coupons only discount products that are **not** already on offer.
- `free_delivery` coupons are the **only** coupon type that still provides value when the cart contains discounted products, because they reduce shipping — not the product price.
- A product "has a discount" when its `offer_price` is set and lower than its `sale_price` (exposed on the Product API as `has_offer: true`).

### What this means for calculations

- The coupon discount is calculated against the **eligible subtotal** — the sum of only the products **without** an offer — not the full cart subtotal.
- The discount is distributed across the eligible (non-discounted) items only. Discounted items keep their offer price untouched (`coupon_discount_per_unit: 0`).
- If **every** product in the cart is already discounted, a `fixed`/`percentage` coupon results in a discount of `0` (the order still completes normally; only a `free_delivery` coupon would have an effect).

### Action required from the frontend

To display a discount that matches what checkout actually charges, the frontend must tell the API which products are in the cart so it knows which are discounted:

- **`/coupon/preview`** and **`/checkout`** already receive `items`, so they are automatically correct — **no change needed**.
- **`/coupon/validate`** now accepts an **optional `items` array**. Send it so the displayed `discount_amount` matches checkout. Without `items`, this endpoint falls back to estimating the discount on the full `subtotal`, which may be higher than the real discount when the cart contains discounted products.

---

## Endpoints

### 1. Validate Coupon Code

Validates a coupon code and returns detailed discount information.

**Endpoint:** `POST /api/v1/coupon/validate`

**Request Body:**

```json
{
  "coupon_code": "RIMAWISTATIC",
  "subtotal": 363.01,
  "items": [
    { "id": 129, "quantity": 1 }
  ]
}
```

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| coupon_code | string | Yes | The coupon code to validate |
| subtotal | numeric | Yes | The cart subtotal before discount (used as the fallback discount base and for the minimum-order check) |
| items | array | No (recommended) | Cart items. **Send this** so the discount is calculated only against non-discounted products and matches checkout. If omitted, the discount is estimated on the full `subtotal`. |
| items.*.id | integer | With `items` | Product ID |
| items.*.quantity | integer | With `items` | Product quantity |

> **Note:** When `items` is provided, `discount_amount` is calculated against the eligible subtotal (products without an offer). The returned `subtotal` field still reflects the full cart subtotal you sent.

**Success Response (200):**

```json
{
  "success": true,
  "message": "Coupon applied successfully",
  "data": {
    "coupon": {
      "code": "RIMAWISTATIC",
      "type": "percentage",
      "value": "10.00",
      "minimum_order_amount": "0.00"
    },
    "is_valid": true,
    "subtotal": 363.01,
    "discount_amount": 36.30,
    "total_after_discount": 326.71,
    "savings": {
      "amount": 36.30,
      "percentage": 10.0
    }
  }
}
```

**Error Response (422):**

```json
{
  "success": false,
  "message": "Invalid coupon",
  "error": "This coupon is not yet valid.",
  "data": {
    "coupon_code": "RIMAWISTATIC",
    "is_valid": false,
    "subtotal": 363.01,
    "discount_amount": 0,
    "total_after_discount": 363.01
  }
}
```

**Possible Error Messages:**

- `Coupon code not found.` - Coupon doesn't exist in database
- `This coupon is inactive.` - Coupon has been deactivated
- `This coupon is not yet valid.` - Current date is before `valid_from`
- `This coupon has expired.` - Current date is after `valid_until`
- `This coupon has reached maximum usage limit.` - Coupon usage exceeded `max_uses`
- `Minimum order amount of X required.` - Subtotal is less than `minimum_order_amount`

---

### 2. Calculate Order Preview

Provides a full preview of order calculations including coupon discounts, itemized breakdown, and totals.

**Endpoint:** `POST /api/v1/coupon/preview`

**Request Body:**

```json
{
  "items": [
    {
      "id": 129,
      "quantity": 1
    }
  ],
  "coupon_code": "RIMAWISTATIC",
  "shipping": 5.00
}
```

**Request Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| items | array | Yes | Array of cart items |
| items.*.id | integer | Yes | Product ID |
| items.*.quantity | integer | Yes | Product quantity |
| coupon_code | string | No | Optional coupon code |
| shipping | numeric | No | Shipping cost (default: 0) |

**Success Response (200):**

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "product_id": 129,
        "quantity": 1,
        "base_price": 363.01,
        "coupon_discount_per_unit": 36.30,
        "unit_sale_price": 326.71,
        "unit_cost_price": 100.00,
        "total_price": 326.71
      }
    ],
    "summary": {
      "subtotal": 363.01,
      "shipping": 5.00,
      "coupon_discount": 36.30,
      "coupon": {
        "code": "RIMAWISTATIC",
        "type": "percentage",
        "value": "10.00"
      },
      "coupon_error": null,
      "total": 331.71
    }
  }
}
```

**Response with Invalid Coupon (200):**

When an invalid coupon is provided, the endpoint still returns 200 but includes an error message:

```json
{
  "success": true,
  "data": {
    "items": [...],
    "summary": {
      "subtotal": 363.01,
      "shipping": 5.00,
      "coupon_discount": 0,
      "coupon": null,
      "coupon_error": "This coupon has expired.",
      "total": 368.01
    }
  }
}
```

---

## Coupon Types

> All product discounts below apply **only to the eligible subtotal** — the products that are **not** already on offer. Products that already have a discount (`has_offer: true`) are excluded.

### 1. Fixed Discount
- Deducts a fixed amount from the **eligible** subtotal (capped at the eligible subtotal)
- **Example:** Coupon value = 20.00, eligible subtotal = 100.00 → Discount = 20.00
- If all cart products are already discounted, eligible subtotal = 0 → Discount = 0

### 2. Percentage Discount
- Deducts a percentage from the **eligible** subtotal
- **Example:** Coupon value = 10 (%), eligible subtotal = 100.00 → Discount = 10.00
- If all cart products are already discounted, eligible subtotal = 0 → Discount = 0

### 3. Free Delivery
- Removes shipping cost from the total; does not touch product prices
- **Example:** Shipping = 5.00 → Discount = 5.00 (from shipping only)
- **The only coupon type that still benefits a cart made entirely of discounted products.**

---

## Frontend Integration Example

### React Example

```jsx
import { useState, useEffect } from 'react';

function CartSummary({ cartItems, shipping, onCouponApplied }) {
  const [couponCode, setCouponCode] = useState('');
  const [discount, setDiscount] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  const validateCoupon = async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch('http://localhost/api/v1/coupon/validate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          coupon_code: couponCode,
          subtotal: subtotal,
          // Send the cart items so the discount is calculated only against
          // products that are NOT already discounted (matches checkout).
          items: cartItems.map((item) => ({
            id: item.id,
            quantity: item.quantity,
          })),
        }),
      });

      const data = await response.json();

      if (data.success) {
        setDiscount(data.data);
        onCouponApplied(data.data.coupon);
      } else {
        setError(data.error);
        setDiscount(null);
      }
    } catch (err) {
      setError('Failed to validate coupon');
    } finally {
      setLoading(false);
    }
  };

  const total = discount
    ? discount.total_after_discount + shipping
    : subtotal + shipping;

  return (
    <div className="cart-summary">
      <h3>Order Summary</h3>

      {/* Coupon Input */}
      <div className="coupon-section">
        <input
          type="text"
          value={couponCode}
          onChange={(e) => setCouponCode(e.target.value.toUpperCase())}
          placeholder="Enter coupon code"
        />
        <button onClick={validateCoupon} disabled={loading}>
          {loading ? 'Validating...' : 'Apply Coupon'}
        </button>
      </div>

      {/* Error Message */}
      {error && <div className="error">{error}</div>}

      {/* Discount Applied */}
      {discount && (
        <div className="discount-applied">
          <div className="coupon-code">Coupon: {discount.coupon.code}</div>
          <div className="savings">
            You save: {discount.savings.amount} JOD
            ({discount.savings.percentage}% off)
          </div>
        </div>
      )}

      {/* Price Breakdown */}
      <div className="price-breakdown">
        <div className="row">
          <span>Subtotal:</span>
          <span>{subtotal.toFixed(2)} JOD</span>
        </div>

        {discount && (
          <div className="row discount">
            <span>Discount:</span>
            <span>-{discount.discount_amount.toFixed(2)} JOD</span>
          </div>
        )}

        <div className="row">
          <span>Shipping:</span>
          <span>{shipping.toFixed(2)} JOD</span>
        </div>

        <div className="row total">
          <span>Total:</span>
          <span>{total.toFixed(2)} JOD</span>
        </div>
      </div>
    </div>
  );
}

export default CartSummary;
```

---

## Error Handling

All endpoints return consistent error responses:

**Validation Errors (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "coupon_code": ["Coupon code is required."],
    "subtotal": ["Subtotal must be a number."]
  }
}
```

**Server Errors (500):**
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## Testing with cURL

### Validate Coupon

```bash
curl -X POST http://localhost/api/v1/coupon/validate \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "coupon_code": "RIMAWISTATIC",
    "subtotal": 363.01,
    "items": [
      {"id": 129, "quantity": 1}
    ]
  }'
```

### Calculate Preview

```bash
curl -X POST http://localhost/api/v1/coupon/preview \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "items": [
      {"id": 129, "quantity": 1}
    ],
    "coupon_code": "RIMAWISTATIC",
    "shipping": 5.00
  }'
```

---

## Notes

1. **Currency:** All amounts are in JOD (Jordanian Dinar)
2. **Precision:** All monetary values are rounded to 2 decimal places
3. **Coupon Usage:** Each coupon can be tracked via `used_count` vs `max_uses`
4. **Validation Order:** Coupons are validated in this order:
   - Existence check
   - Active status
   - Start date (`valid_from`)
   - End date (`valid_until`)
   - Usage limit
   - Minimum order amount
5. **Free Delivery:** For `free_delivery` type coupons, the discount is applied to shipping, not subtotal
6. **Discounted products:** `fixed`/`percentage` coupons apply only to products **without** an active offer. Products with an offer (`has_offer: true`) are excluded from the discount. `free_delivery` is unaffected by this rule.

---

## Common Issues & Solutions

### Issue: "This coupon is not yet valid"
**Cause:** The coupon's `valid_from` date is in the future.
**Solution:** Update the coupon in admin panel to set `valid_from` to today or earlier.

### Issue: "This coupon has expired"
**Cause:** The coupon's `valid_until` date has passed.
**Solution:** Extend the `valid_until` date in admin panel.

### Issue: "Minimum order amount required"
**Cause:** Cart subtotal is less than coupon's `minimum_order_amount`.
**Solution:** Add more items to cart or adjust coupon requirements.

---

## Database Schema Reference

```sql
CREATE TABLE coupons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    type ENUM('fixed', 'percentage', 'free_delivery') NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    minimum_order_amount DECIMAL(10, 2) DEFAULT 0,
    max_uses INT UNSIGNED NULL,
    used_count INT UNSIGNED DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
