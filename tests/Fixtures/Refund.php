<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Refunds\Refund as CashierRefund;

class Refund extends CashierRefund
{
    protected $table = 'refunds';
}
