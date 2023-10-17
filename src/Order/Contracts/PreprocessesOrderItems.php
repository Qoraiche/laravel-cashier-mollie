<?php

namespace Laravel\Cashier\Mollie\Order\Contracts;

use Laravel\Cashier\Mollie\Order\OrderItem;

interface PreprocessesOrderItems
{
    /**
     * Called right before processing the order item into an order.
     *
     * @param  OrderItem  $item
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public static function preprocessOrderItem(OrderItem $item);
}
