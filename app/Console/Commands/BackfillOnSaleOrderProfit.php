<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillOnSaleOrderProfit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:backfill-onsale-profit
                            {--dry-run : Preview the changes without writing them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct historical order items where an on-sale product recorded the regular sale_price instead of its offer_price (inflated profit).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->warn('Note: the historical offer_price at order time is not stored. This');
        $this->warn('backfill uses each product\'s CURRENT offer_price as the best estimate.');
        $this->warn('Items whose offer has since changed/ended cannot be reconstructed exactly.');
        $this->newLine();

        // Affected rows = the old checkout bug fingerprint:
        // no per-unit coupon, unit price == base price == product's current regular price,
        // and the product currently has an active offer cheaper than that regular price.
        $items = OrderItem::query()
            ->select('order_items.*')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereColumn('order_items.unit_sale_price', 'order_items.base_price')
            ->where('order_items.coupon_discount_per_unit', 0)
            ->whereNotNull('products.offer_price')
            ->whereColumn('products.offer_price', '<', 'products.sale_price')
            ->whereColumn('order_items.unit_sale_price', '=', 'products.sale_price')
            ->with(['product:id,name,offer_price,sale_price', 'order:id,order_number'])
            ->get();

        if ($items->isEmpty()) {
            $this->info('No affected order items found. Nothing to backfill.');
            return self::SUCCESS;
        }

        $rows = [];
        $totalProfitDelta = 0.0;

        foreach ($items as $item) {
            $offerPrice = (float) $item->product->offer_price;
            $oldUnit = (float) $item->unit_sale_price;
            $newTotal = round($offerPrice * $item->quantity, 2);
            $profitDelta = round(($oldUnit - $offerPrice) * $item->quantity, 2);
            $totalProfitDelta += $profitDelta;

            $rows[] = [
                $item->order->order_number,
                $item->product->name,
                number_format($oldUnit, 2),
                number_format($offerPrice, 2),
                $item->quantity,
                number_format($profitDelta, 2),
            ];
        }

        $this->table(
            ['Order', 'Product', 'Old unit', 'New unit', 'Qty', 'Profit Δ'],
            $rows
        );
        $this->info(sprintf(
            '%d order item(s) affected. Total reported profit will drop by %s.',
            $items->count(),
            number_format($totalProfitDelta, 2)
        ));

        if ($dryRun) {
            $this->newLine();
            $this->comment('Dry run — no changes written. Re-run without --dry-run to apply.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $offerPrice = (float) $item->product->offer_price;
                $item->base_price = $offerPrice;
                $item->unit_sale_price = $offerPrice;
                $item->total_price = round($offerPrice * $item->quantity, 2);
                $item->save();
            }
        });

        $this->info(sprintf('Backfill complete: %d order item(s) corrected.', $items->count()));

        return self::SUCCESS;
    }
}
