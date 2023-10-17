<?php

namespace Laravel\Cashier\Mollie\Order;

abstract class BaseOrderItemPreprocessor
{
    /**
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    abstract public function handle(OrderItemCollection $items);
}
