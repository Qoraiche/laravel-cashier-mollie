<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Refunds\Refund as CashierRefund;

class Refund extends CashierRefund
{
    protected $table = 'refunds';
}
