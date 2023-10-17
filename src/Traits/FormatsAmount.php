<?php

namespace Cashier\Mollie\Traits;

use Cashier\Mollie\Cashier;
use Money\Money;

trait FormatsAmount
{
    /**
     * Format the given amount into a string.
     *
     * @param  \Money\Money  $amount
     * @return string
     */
    protected function formatAmount(Money $amount)
    {
        return Cashier::formatAmount($amount);
    }
}
