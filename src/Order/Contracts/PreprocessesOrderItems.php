<?php

namespace Cashier\Mollie\Order\Contracts;

use Cashier\Mollie\Order\OrderItem;

interface PreprocessesOrderItems
{
    /**
     * Called right before processing the order item into an order.
     *
     * @param  OrderItem  $item
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public static function preprocessOrderItem(OrderItem $item);
}
