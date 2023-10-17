<?php

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Subscription;

class SubscriptionResumed
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Subscription
     */
    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
