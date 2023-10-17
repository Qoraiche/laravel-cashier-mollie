<?php

declare(strict_types=1);

namespace Cashier\Mollie\Refunds\Contracts;

use Cashier\Mollie\Refunds\RefundItem;

interface IsRefundable
{
    /**
     * @param  \Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return void
     */
    public static function handlePaymentRefunded(RefundItem $refundItem);

    /**
     * @param  \Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return void
     */
    public static function handlePaymentRefundFailed(RefundItem $refundItem);
}
