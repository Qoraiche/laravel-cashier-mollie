<?php

declare(strict_types=1);

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Refunds\Refund;

class RefundProcessed
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Refunds\Refund
     */
    public Refund $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }
}
