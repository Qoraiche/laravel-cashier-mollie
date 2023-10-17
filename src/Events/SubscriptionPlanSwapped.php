<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Subscription;

class SubscriptionPlanSwapped
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Subscription
     */
    public $subscription;

    /**
     * The previous subscription plan before swapping if exists.
     *
     * @var mixed
     */
    public $previousPlan;

    public function __construct(Subscription $subscription, $previousPlan = null)
    {
        $this->subscription = $subscription;

        $this->previousPlan = $previousPlan;
    }
}
