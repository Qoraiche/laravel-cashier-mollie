<?php

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Subscription;

class SubscriptionQuantityUpdated
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Subscription
     */
    public $subscription;

    /**
     * @var int
     */
    public $oldQuantity;

    public function __construct(Subscription $subscription, int $oldQuantity)
    {
        $this->subscription = $subscription;
        $this->oldQuantity = $oldQuantity;
    }
}
