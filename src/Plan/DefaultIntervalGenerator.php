<?php

namespace Cashier\Mollie\Plan;

use Illuminate\Support\Str;
use Cashier\Mollie\Plan\Contracts\IntervalGeneratorContract;
use Cashier\Mollie\Subscription;

class DefaultIntervalGenerator extends BaseIntervalGenerator implements IntervalGeneratorContract
{
    /**
     * @var string
     */
    protected $interval;

    public function __construct(string $interval)
    {
        $this->interval = $interval;
        $this->useCarbonThisDayOrLast();
    }

    /**
     * @param  \Cashier\Mollie\Subscription|null  $subscription
     * @return \Carbon\Carbon|\Carbon\Traits\Modifiers
     */
    public function getEndOfNextSubscriptionCycle(Subscription $subscription = null)
    {
        $cycle_ends_at = $subscription->cycle_ends_at ?? now();
        $subscription_date = $this->startOfTheSubscription($subscription);

        if ($this->isMonthly()) {
            return $cycle_ends_at->addMonthsNoOverflow((int) filter_var($this->interval, FILTER_SANITIZE_NUMBER_INT))
                ->thisDayOrLastOfTheMonth($subscription_date);
        }

        return $cycle_ends_at->modify('+'.$this->interval);
    }

    /**
     * @return bool
     */
    protected function isMonthly()
    {
        return Str::contains($this->interval, 'month');
    }
}
