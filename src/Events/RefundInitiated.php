<?php

declare(strict_types=1);

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Refunds\Refund;

class RefundInitiated
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Refunds\Refund
     */
    public Refund $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }
}
