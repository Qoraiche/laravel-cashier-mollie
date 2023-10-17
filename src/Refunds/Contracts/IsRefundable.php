<?php

declare(strict_types=1);

namespace Laravel\Cashier\Mollie\Refunds\Contracts;

use Laravel\Cashier\Mollie\Refunds\RefundItem;

interface IsRefundable
{
    /**
     * @param  \Laravel\Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return void
     */
    public static function handlePaymentRefunded(RefundItem $refundItem);

    /**
     * @param  \Laravel\Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return void
     */
    public static function handlePaymentRefundFailed(RefundItem $refundItem);
}
