<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Credit\Credit;

class BalanceTurnedStale
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Credit\Credit
     */
    public $credit;

    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
