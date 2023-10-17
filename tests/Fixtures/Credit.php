<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Credit\Credit as CashierCredit;

class Credit extends CashierCredit
{
    protected $table = 'credits';
}
