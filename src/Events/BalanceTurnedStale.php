<?php

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Credit\Credit;

class BalanceTurnedStale
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Credit\Credit
     */
    public $credit;

    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
