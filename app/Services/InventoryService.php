<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;

/**
 * Inventory Service
 *
 * Handles inventory-related business logic:
 * - Stock quantity calculations
 * - Transaction validation
 * - Stock projection
 * - Transaction type validation
 *
 * Extracted from Product model for better separation of concerns.
 */
class InventoryService
{
    /**
     * Calculate current stock quantity for a product.
     *
     * Uses optimized single-query calculation.
     * Calculation: supply + return - sale - damage +/- adjustment
     *
     * @param Product $product
     * @return int
     */
    public function calculateCurrentStock(Product $product): int
    {
        return (int) $product->inventoryTransactions()
            ->selectRaw('SUM(CASE
                WHEN type IN ("supply", "return") THEN quantity
                WHEN type IN ("sale", "damage") THEN -quantity
                WHEN type = "adjustment" THEN quantity
                ELSE 0
            END) as total')
            ->value('total') ?? 0;
    }

    /**
     * Calculate projected stock after a transaction.
     *
     * @param Product $product
     * @param string $type Transaction type
     * @param int $quantity Transaction quantity
     * @return int|null Projected stock or null if product doesn't exist yet
     */
    public function calculateProjectedStock(Product $product, string $type, int $quantity): ?int
    {
        $currentStock = $this->calculateCurrentStock($product);

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
     * @param Product $product
     * @param string $type Transaction type
     * @param int $quantity Transaction quantity
     * @return bool True if transaction is safe (stock >= 0)
     */
    public function canApplyTransaction(Product $product, string $type, int $quantity): bool
    {
        $projectedStock = $this->calculateProjectedStock($product, $type, $quantity);

        return $projectedStock !== null && $projectedStock >= 0;
    }

    /**
     * Get available transaction types.
     *
     * @return array<string, string>
     */
    public function getAvailableTypes(): array
    {
        return [
            InventoryTransaction::TYPE_SUPPLY,
            InventoryTransaction::TYPE_SALE,
            InventoryTransaction::TYPE_RETURN,
            InventoryTransaction::TYPE_DAMAGE,
            InventoryTransaction::TYPE_ADJUSTMENT,
        ];
    }

    /**
     * Check if a transaction type increases stock.
     *
     * @param string $type
     * @return bool
     */
    public function transactionIncreasesStock(string $type): bool
    {
        return in_array($type, [
            InventoryTransaction::TYPE_SUPPLY,
            InventoryTransaction::TYPE_RETURN
        ]) || ($type === InventoryTransaction::TYPE_ADJUSTMENT);
    }

    /**
     * Check if a transaction type decreases stock.
     *
     * @param string $type
     * @return bool
     */
    public function transactionDecreasesStock(string $type): bool
    {
        return in_array($type, [
            InventoryTransaction::TYPE_SALE,
            InventoryTransaction::TYPE_DAMAGE
        ]);
    }

    /**
     * Get transaction type label for display.
     *
     * @param string $type
     * @return string
     */
    public function getTransactionTypeLabel(string $type): string
    {
        return match ($type) {
            InventoryTransaction::TYPE_SUPPLY => 'Supply',
            InventoryTransaction::TYPE_SALE => 'Sale',
            InventoryTransaction::TYPE_RETURN => 'Return',
            InventoryTransaction::TYPE_DAMAGE => 'Damage',
            InventoryTransaction::TYPE_ADJUSTMENT => 'Adjustment',
            default => 'Unknown',
        };
    }

    /**
     * Validate transaction type.
     *
     * @param string $type
     * @return bool
     */
    public function isValidTransactionType(string $type): bool
    {
        return in_array($type, $this->getAvailableTypes());
    }

    /**
     * Get stock for multiple products in optimized way.
     *
     * @param array $productIds
     * @return array<int, int> Product ID => Stock quantity
     */
    public function getStockForProducts(array $productIds): array
    {
        $stocks = InventoryTransaction::whereIn('product_id', $productIds)
            ->selectRaw('product_id, SUM(CASE
                WHEN type IN ("supply", "return") THEN quantity
                WHEN type IN ("sale", "damage") THEN -quantity
                WHEN type = "adjustment" THEN quantity
                ELSE 0
            END) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id')
            ->toArray();

        // Fill missing products with 0
        foreach ($productIds as $productId) {
            if (!isset($stocks[$productId])) {
                $stocks[$productId] = 0;
            }
        }

        return $stocks;
    }

    /**
     * Validate stock availability for order items.
     *
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @return array<string> Array of error messages (empty if valid)
     */
    public function validateStockAvailability(array $items): array
    {
        $errors = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $errors[] = "Product ID {$item['product_id']} not found.";
                continue;
            }

            $currentStock = $this->calculateCurrentStock($product);
            $projectedStock = $currentStock - $item['quantity'];

            if ($projectedStock < 0) {
                $errors[] = "Product '{$product->name}' has insufficient stock. Available: {$currentStock}, Required: {$item['quantity']}.";
            }
        }

        return $errors;
    }

    /**
     * Decrease stock for order items (creates sale transactions).
     *
     * @param Order $order
     * @return void
     */
    public function decreaseStockForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            InventoryTransaction::create([
                'product_id' => $item->product_id,
                'type' => InventoryTransaction::TYPE_SALE,
                'quantity' => $item->quantity,
                'notes' => "Order #{$order->order_number}",
            ]);
        }
    }

    /**
     * Restore stock for order items (creates return transactions).
     *
     * @param Order $order
     * @return void
     */
    public function restoreStockForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            InventoryTransaction::create([
                'product_id' => $item->product_id,
                'type' => InventoryTransaction::TYPE_RETURN,
                'quantity' => $item->quantity,
                'notes' => "Restored from order #{$order->order_number}",
            ]);
        }
    }
}
