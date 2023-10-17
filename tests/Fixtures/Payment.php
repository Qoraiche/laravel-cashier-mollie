<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Payment as CashierPayment;

class Payment extends CashierPayment
{
    protected $table = 'payments';
}
