<?php

namespace Cashier\Mollie\Order;

class PersistOrderItemsPreprocessor extends BaseOrderItemPreprocessor
{
    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(OrderItemCollection $items)
    {
        return $items->save();
    }
}
