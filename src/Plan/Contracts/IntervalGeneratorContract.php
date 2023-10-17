<?php

namespace Cashier\Mollie\Plan\Contracts;

use Cashier\Mollie\Subscription;

interface IntervalGeneratorContract
{
    /**
     * @param  \Cashier\Mollie\Subscription  $subscription
     * @return \Carbon\Carbon
     */
    public function getEndOfNextSubscriptionCycle(Subscription $subscription = null);
}
