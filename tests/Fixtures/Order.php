<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Order\Order as CashierOrder;

class Order extends CashierOrder
{
    protected $table = 'orders';
}
