<?php

namespace Cashier\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMollieMethodMaximumAmount;
use Cashier\Mollie\Order\Contracts\MaximumPayment as MaximumPaymentContract;
use Mollie\Api\Resources\Mandate;

class MaximumPayment implements MaximumPaymentContract
{
    /**
     * @param  \Mollie\Api\Resources\Mandate  $mandate
     * @param $currency
     * @return \Money\Money
     */
    public static function forMollieMandate(Mandate $mandate, $currency)
    {
        /** @var GetMollieMethodMaximumAmount $getMaximumAmount */
        $getMaximumAmount = app()->make(GetMollieMethodMaximumAmount::class);

        return $getMaximumAmount->execute($mandate->method, $currency);
    }
}
