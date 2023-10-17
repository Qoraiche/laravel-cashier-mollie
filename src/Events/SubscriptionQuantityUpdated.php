<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Subscription;

class SubscriptionQuantityUpdated
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Subscription
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
