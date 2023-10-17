<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Order\OrderItem as CashierOrderItem;

class OrderItem extends CashierOrderItem
{
    protected $table = 'order_items';
}
