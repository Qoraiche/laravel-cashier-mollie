<?php

namespace Cashier\Mollie\Order\Contracts;

use Mollie\Api\Resources\Mandate;

interface MinimumPayment
{
    /**
     * @param  \Mollie\Api\Resources\Mandate  $mandate
     * @param $currency
     * @return \Money\Money
     */
    public static function forMollieMandate(Mandate $mandate, $currency);
}
