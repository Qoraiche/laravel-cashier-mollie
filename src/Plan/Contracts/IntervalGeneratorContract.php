<?php

namespace Laravel\Cashier\Mollie\Plan\Contracts;

use Laravel\Cashier\Mollie\Subscription;

interface IntervalGeneratorContract
{
    /**
     * @param  \Laravel\Cashier\Mollie\Subscription  $subscription
     * @return \Carbon\Carbon
     */
    public function getEndOfNextSubscriptionCycle(Subscription $subscription = null);
}
