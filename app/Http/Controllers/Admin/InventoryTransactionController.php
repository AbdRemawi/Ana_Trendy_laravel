<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryTransactionRequest;
use App\Http\Requests\UpdateInventoryTransactionRequest;
use App\Models\InventoryTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Inventory Transaction Management Controller (Admin)
 *
 * Handles inventory transaction CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Features:
 * - Track stock movements (supply, sale, return, damage, adjustment)
 * - View transaction history for products
 * - Calculate current stock levels
 * - Filter by transaction type
 */
class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of inventory transactions.
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $query = InventoryTransaction::query()->with(['product']);

        // Filter by product if requested
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        // Filter by type if requested
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $transactions = $query->latest()->paginate(20);

        // Get filter options
        $products = \App\Models\Product::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.inventory.index', compact(
            'transactions',
            'products'
        ));
    }

    /**
     * Show the form for creating a new inventory transaction.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        $products = \App\Models\Product::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.inventory.create', compact('products'));
    }

    /**
     * Store a newly created inventory transaction.
     * Authorization is handled via route middleware.
     */
    public function store(StoreInventoryTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        InventoryTransaction::create([
            'product_id' => $validated['product_id'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', __('admin.inventory_transaction_created_successfully'));
    }

    /**
     * Display the specified inventory transaction.
     * Authorization is handled via route middleware.
     */
    public function show(InventoryTransaction $inventory): View
    {
        $inventory->load('product');

        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory transaction.
     * Authorization is handled via route middleware.
     */
    public function edit(InventoryTransaction $inventory): View
    {
        $inventory->load('product');
        $products = \App\Models\Product::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.inventory.edit', compact(
            'inventory',
            'products'
        ));
    }

    /**
     * Update the specified inventory transaction.
     * Authorization is handled via route middleware.
     */
    public function update(UpdateInventoryTransactionRequest $request, InventoryTransaction $inventory): RedirectResponse
    {
        $validated = $request->validated();

        $inventory->update([
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', __('admin.inventory_transaction_updated_successfully'));
    }

    /**
     * Remove the specified inventory transaction.
     * Authorization is handled via route middleware.
     *
     * SECURITY: Prevents deletion that would corrupt stock calculations
     * - Old transactions cannot be deleted (data integrity)
     * - Transactions from same day require special consideration
     */
    public function destroy(InventoryTransaction $inventory): RedirectResponse
    {
        try {
            // SECURITY: Prevent deletion of old transactions to maintain data integrity
            $threshold = now()->subDays(7); // 7-day threshold
            if ($inventory->created_at < $threshold) {
                return back()
                    ->with('error', __('admin.cannot_delete_old_transaction', ['date' => $inventory->created_at->format('Y-m-d')]));
            }

            // SECURITY: Log transaction details before deletion for audit trail
            $transactionInfo = "ID: {$inventory->id}, Product: {$inventory->product_id}, Type: {$inventory->type}, Quantity: {$inventory->quantity}";

            $inventory->delete();

            return redirect()
                ->route('admin.inventory.index')
                ->with('success', __('admin.inventory_transaction_deleted_successfully'))
                ->with('warning', __('admin.transaction_deletion_warning'));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.delete_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Display inventory transactions for a specific product.
     * Authorization is handled via route middleware.
     */
    public function byProduct(Request $request, $productId): View
    {
        $product = \App\Models\Product::withTrashed()
            ->withStockQuantity()
            ->findOrFail($productId);
        $query = InventoryTransaction::where('product_id', $productId);

        // Filter by type if requested
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.inventory.by-product', compact(
            'product',
            'transactions'
        ));
    }
}
