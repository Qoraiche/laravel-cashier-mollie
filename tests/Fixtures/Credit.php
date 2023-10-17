<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Credit\Credit as CashierCredit;

class Credit extends CashierCredit
{
    protected $table = 'credits';
}
