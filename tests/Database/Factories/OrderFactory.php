<?php

namespace Cashier\Mollie\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Tests\Fixtures\User;

class OrderFactory extends Factory
{
    /**
     * Get the name of the model that is generated by the factory.
     *
     * @return string
     */
    public function modelName()
    {
        return Cashier::$orderModel;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'owner_type' => User::class,
            'currency' => 'EUR',
            'subtotal' => 123,
            'tax' => 0,
            'total' => 123,
            'total_due' => 123,
            'number' => '2018-0000-0001',
            'credit_used' => 0,
            'balance_before' => 0,
        ];
    }
}
