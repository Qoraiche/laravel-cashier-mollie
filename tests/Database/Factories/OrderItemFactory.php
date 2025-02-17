<?php

namespace Cashier\Mollie\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Tests\Fixtures\User;

class OrderItemFactory extends Factory
{
    /**
     * Get the name of the model that is generated by the factory.
     *
     * @return string
     */
    public function modelName()
    {
        return Cashier::$orderItemModel;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_type' => User::class,
            'owner_id' => 1,
            'orderable_type' => Cashier::$subscriptionModel,
            'orderable_id' => 1,
            'description' => 'Some dummy description',
            'unit_price' => 12150,
            'quantity' => 1,
            'tax_percentage' => 21.5,
            'currency' => 'EUR',
            'process_at' => now()->subMinute(),
        ];
    }

    public function unlinked()
    {
        return $this->state(fn () => [
            'orderable_type' => null,
            'orderable_id' => null,
        ]);
    }

    public function unprocessed()
    {
        return $this->state(fn () => [
            'order_id' => null,
        ]);
    }

    public function processed()
    {
        return $this->state(fn () => [
            'order_id' => 1,
        ]);
    }

    public function EUR()
    {
        return $this->state(fn () => [
            'currency' => 'EUR',
        ]);
    }

    public function USD()
    {
        return $this->state(fn () => [
            'currency' => 'USD',
        ]);
    }
}
