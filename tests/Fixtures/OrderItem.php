<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Order\OrderItem as CashierOrderItem;

class OrderItem extends CashierOrderItem
{
    protected $table = 'order_items';
}
