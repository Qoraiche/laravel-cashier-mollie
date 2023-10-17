<?php

namespace Cashier\Mollie\Order;

abstract class BaseOrderItemPreprocessor
{
    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    abstract public function handle(OrderItemCollection $items);
}
