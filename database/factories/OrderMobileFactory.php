<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderMobile>
 */
class OrderMobileFactory extends Factory
{
    /**
     * Jordanian mobile phone prefixes (079, 078, 077)
     */
    private const array MOBILE_PREFIXES = ['079', '078', '077'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefix = fake()->randomElement(self::MOBILE_PREFIXES);
        $phoneNumber = $prefix . fake()->numerify('#######');

        return [
            'order_id' => \App\Models\Order::factory(),
            'phone_number' => $phoneNumber,
        ];
    }

    /**
     * Create an order mobile for a specific order.
     */
    public function forOrder($orderId): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $orderId,
        ]);
    }

    /**
     * Create a mobile with a specific phone number.
     */
    public function withPhoneNumber($phoneNumber): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_number' => $phoneNumber,
        ]);
    }
}
