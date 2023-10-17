<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Refunds\RefundItem as CashierRefundItem;

class RefundItem extends CashierRefundItem
{
    protected $table = 'refund_items';
}
