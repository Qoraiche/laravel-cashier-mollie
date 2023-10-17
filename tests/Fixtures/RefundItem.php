<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Refunds\RefundItem as CashierRefundItem;

class RefundItem extends CashierRefundItem
{
    protected $table = 'refund_items';
}
