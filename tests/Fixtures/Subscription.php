<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    protected $table = 'subscriptions';
}
