<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMobile;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Order Seeder
 *
 * Seeds 30 realistic orders with:
 * - Different statuses (processing, with_delivery_company, received, cancelled, returned)
 * - Spread across the last 30 days
 * - 1-5 items per order
 * - 1-2 phone numbers per order
 * - Realistic pricing and profit calculations
 * - Inventory transactions for sold items
 *
 * All orders reference existing products, cities, and delivery couriers.
 */
class OrderSeeder extends Seeder
{
    /**
     * Number of orders to create.
     */
    protected const int ORDER_COUNT = 30;

    /**
     * Jordanian names for realistic data.
     */
    private const array JORDANIAN_NAMES = [
        'Ahmad Al-Ahmad', 'Faisal Mohammad', 'Omar Khalil', 'Kareem Saeed',
        'Yousef Hassan', 'Ali Rahman', 'Hamza Fawzi', 'Zaid Jad',
        'Rami Nabil', 'Sami Tarek', 'Mahmoud Waleed', 'Khaled Basel',
        'Jamil Bashar', 'Tamer Sameh', 'Mazen Anas', 'Raed Faris',
        'Safwan Raed', 'Hadi Laith', 'Wael Ammar', 'Qasim Marwan',
        'Layla Khalid', 'Noura Yasin', 'Rana Sami', 'Dana Hani',
        'Hala Mousa', 'Sara Jaber', 'Leena Rami', 'Yara Tamer',
        'Reem Khalid', 'Fatima Omar',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📦 Seeding orders...');

        // Validate prerequisites
        $productCount = Product::where('status', 'active')->count();
        $cityCount = \App\Models\City::where('is_active', true)->count();
        $courierCount = \App\Models\DeliveryCourier::where('is_active', true)->count();

        if ($productCount === 0) {
            $this->command->warn('   ⚠️  No products found. Please run ProductSeeder first.');
            return;
        }

        if ($cityCount === 0) {
            $this->command->warn('   ⚠️  No cities found. Please create cities first.');
            return;
        }

        if ($courierCount === 0) {
            $this->command->warn('   ⚠️  No delivery couriers found. Please create couriers first.');
            return;
        }

        $this->command->info("   Found {$productCount} products, {$cityCount} cities, {$courierCount} couriers.");
        $this->command->newLine();

        // Create orders with items, mobiles, and inventory adjustments
        $createdOrders = 0;
        $statusCounts = [
            'received' => 0,
            'processing' => 0,
            'with_delivery_company' => 0,
            'cancelled' => 0,
            'returned' => 0,
        ];

        for ($i = 1; $i <= self::ORDER_COUNT; $i++) {
            DB::transaction(function () use ($i, &$createdOrders, &$statusCounts) {
                $order = $this->createOrder($i);
                $this->createOrderItems($order);
                $this->createOrderMobiles($order);

                // Track counts
                $statusCounts[$order->status]++;
                $createdOrders++;

                // Show progress every 5 orders
                if ($createdOrders % 5 === 0) {
                    $this->command->info("   Created {$createdOrders}/" . self::ORDER_COUNT . " orders...");
                }
            });
        }

        $this->command->newLine();
        $this->command->info('✅ Successfully seeded ' . $createdOrders . ' orders.');
        $this->command->newLine();
        $this->command->info('   Order Status Breakdown:');
        foreach ($statusCounts as $status => $count) {
            $label = match($status) {
                'received' => 'Delivered',
                'processing' => 'Pending',
                'with_delivery_company' => 'In Transit',
                'cancelled' => 'Cancelled',
                'returned' => 'Returned',
            };
            $this->command->info("   - {$label}: {$count}");
        }
        $this->command->newLine();
    }

    /**
     * Create a single order with realistic data.
     */
    protected function createOrder(int $index): Order
    {
        // Get random city and courier
        $city = \App\Models\City::inRandomOrder()->first();
        $courier = \App\Models\DeliveryCourier::inRandomOrder()->first();

        // Get random coupon (30% chance of having a coupon)
        $coupon = fake()->boolean(30)
            ? \App\Models\Coupon::where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('valid_until')
                        ->orWhere('valid_until', '>', now());
                })
                ->inRandomOrder()
                ->first()
            : null;

        // Generate order items data
        $itemCount = fake()->numberBetween(1, 5);
        $products = Product::where('status', 'active')
            ->inRandomOrder()
            ->limit($itemCount)
            ->get();

        $subtotalProducts = 0;
        $couponDiscountAmount = 0;
        $freeDeliveryDiscount = 0;

        foreach ($products as $product) {
            $qty = fake()->numberBetween(1, 3);
            $price = $product->offer_price ?? $product->sale_price;
            $subtotalProducts += $price * $qty;
        }

        // Apply coupon discount if applicable
        if ($coupon && $subtotalProducts >= $coupon->minimum_order_amount) {
            if ($coupon->type === 'fixed') {
                $couponDiscountAmount = min($coupon->value, $subtotalProducts);
            } elseif ($coupon->type === 'percentage') {
                $couponDiscountAmount = $subtotalProducts * ($coupon->value / 100);
            } elseif ($coupon->type === 'free_delivery') {
                $freeDeliveryDiscount = fake()->randomFloat(2, 3, 8);
            }
        }

        // Delivery fees
        $realDeliveryFee = fake()->randomFloat(3, 3, 10);

        // Calculate final charge
        $actualCharge = $subtotalProducts + $realDeliveryFee - $couponDiscountAmount - $freeDeliveryDiscount;
        $totalPriceForCustomer = max($actualCharge, 0);

        // Generate order number
        $orderNumber = 'ORD-' . str_pad((string) $index, 6, '0', STR_PAD_LEFT);

        // Determine status with realistic distribution (50% received, 20% processing, 15% with_delivery, 10% cancelled, 5% returned)
        $statusArray = array_merge(
            array_fill(0, 50, 'received'),
            array_fill(0, 20, 'processing'),
            array_fill(0, 15, 'with_delivery_company'),
            array_fill(0, 10, 'cancelled'),
            array_fill(0, 5, 'returned'),
        );
        $status = fake()->randomElement($statusArray);

        // Random date within last 30 days
        $createdAt = fake()->dateTimeBetween('-30 days', 'now');

        // Create the order
        $order = Order::create([
            'order_number' => $orderNumber,
            'full_name' => fake()->randomElement(self::JORDANIAN_NAMES),
            'city_id' => $city->id,
            'address' => fake()->streetAddress() . ', ' . fake()->secondaryAddress(),
            'delivery_courier_id' => fake()->boolean(80) ? $courier->id : null, // 80% have courier
            'real_delivery_fee' => $realDeliveryFee,
            'subtotal_products' => round($subtotalProducts, 2),
            'coupon_id' => $coupon?->id,
            'coupon_discount_amount' => round($couponDiscountAmount, 2),
            'free_delivery_discount' => $freeDeliveryDiscount > 0 ? round($freeDeliveryDiscount, 3) : null,
            'actual_charge' => round($actualCharge, 2),
            'total_price_for_customer' => round($totalPriceForCustomer, 2),
            'status' => $status,
            'notes' => fake()->optional(30)->sentence(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Store products for later use
        $order->products_data = $products;

        return $order;
    }

    /**
     * Create order items for an order.
     */
    protected function createOrderItems(Order $order): void
    {
        foreach ($order->products_data as $product) {
            $quantity = fake()->numberBetween(1, 3);
            $basePrice = $product->sale_price;
            $salePrice = $product->offer_price ?? $product->sale_price;
            $discountPerUnit = max(0, $basePrice - $salePrice);
            $totalPrice = $salePrice * $quantity;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'base_price' => $basePrice,
                'coupon_discount_per_unit' => $discountPerUnit,
                'unit_sale_price' => $salePrice,
                'unit_cost_price' => $product->cost_price,
                'total_price' => round($totalPrice, 2),
            ]);

            // Create inventory sale transaction if order is not cancelled/returned
            if (!in_array($order->status, ['cancelled', 'returned'])) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => $quantity,
                    'notes' => "Order #{$order->order_number}",
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }
        }
    }

    /**
     * Create order mobiles for an order (1-2 phone numbers).
     */
    protected function createOrderMobiles(Order $order): void
    {
        $mobileCount = fake()->boolean(70) ? 1 : 2; // 70% have 1 number, 30% have 2

        for ($i = 0; $i < $mobileCount; $i++) {
            $prefix = fake()->randomElement(['079', '078', '077']);
            $phoneNumber = $prefix . fake()->numerify('#######');

            OrderMobile::create([
                'order_id' => $order->id,
                'phone_number' => $phoneNumber,
            ]);
        }
    }
}
