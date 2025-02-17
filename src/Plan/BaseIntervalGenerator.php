<?php

namespace Cashier\Mollie\Plan;

use Carbon\Carbon;
use Cashier\Mollie\Subscription;

class BaseIntervalGenerator
{
    /**
     * @param  \Cashier\Mollie\Subscription|null  $subscription
     * @return \Carbon\Carbon
     */
    protected function startOfTheSubscription(Subscription $subscription = null)
    {
        if (isset($subscription->trial_ends_at) && ! is_null($subscription->trial_ends_at)) {
            return $subscription->trial_ends_at;
        }

        return $subscription->created_at ?? now();
    }

    protected function useCarbonThisDayOrLast()
    {
        Carbon::macro('thisDayOrLastOfTheMonth', function ($startOfTheSubscription) {
            $last = $this->lastOfMonth();

            $this->day = ($startOfTheSubscription->day > $last->day) ? $last->day : $startOfTheSubscription->day;

            return $this->parse($this->format('Y-m-d').' '.$startOfTheSubscription->format('H:i:s'));
        });
    }
}
