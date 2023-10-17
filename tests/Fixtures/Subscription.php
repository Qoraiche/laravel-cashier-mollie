<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    protected $table = 'subscriptions';
}
