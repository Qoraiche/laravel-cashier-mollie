<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Payment as CashierPayment;

class Payment extends CashierPayment
{
    protected $table = 'payments';
}
