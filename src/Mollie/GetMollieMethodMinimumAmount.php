<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Money\Money;

class GetMollieMethodMinimumAmount extends BaseMollieInteraction implements Contracts\GetMollieMethodMinimumAmount
{
    public function execute(string $method, string $currency): Money
    {
        $minimumAmount = $this->mollie
            ->methods()
            ->get($method, ['currency' => $currency])
            ->minimumAmount;

        return mollie_object_to_money($minimumAmount);
    }
}
