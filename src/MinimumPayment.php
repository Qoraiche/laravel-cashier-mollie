<?php

namespace Cashier\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMollieMethodMinimumAmount;
use Cashier\Mollie\Order\Contracts\MinimumPayment as MinimumPaymentContract;
use Mollie\Api\Resources\Mandate;

class MinimumPayment implements MinimumPaymentContract
{
    /**
     * @param  \Mollie\Api\Resources\Mandate  $mandate
     * @param $currency
     * @return \Money\Money
     */
    public static function forMollieMandate(Mandate $mandate, $currency)
    {
        /** @var GetMollieMethodMinimumAmount $getMinimumAmount */
        $getMinimumAmount = app()->make(GetMollieMethodMinimumAmount::class);

        return $getMinimumAmount->execute($mandate->method, $currency);
    }
}
