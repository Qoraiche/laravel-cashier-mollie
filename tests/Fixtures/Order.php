<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Order\Order as CashierOrder;

class Order extends CashierOrder
{
    protected $table = 'orders';
}
