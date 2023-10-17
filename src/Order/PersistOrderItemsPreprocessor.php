<?php

namespace Laravel\Cashier\Mollie\Order;

class PersistOrderItemsPreprocessor extends BaseOrderItemPreprocessor
{
    /**
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(OrderItemCollection $items)
    {
        return $items->save();
    }
}
