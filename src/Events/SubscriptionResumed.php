<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Subscription;

class SubscriptionResumed
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Subscription
     */
    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
