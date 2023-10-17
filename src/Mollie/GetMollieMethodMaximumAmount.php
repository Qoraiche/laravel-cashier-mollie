<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Money\Money;

class GetMollieMethodMaximumAmount extends BaseMollieInteraction implements Contracts\GetMollieMethodMaximumAmount
{
    public function execute(string $method, string $currency): ?Money
    {
        $maximumAmount = $this->mollie
            ->methods()
            ->get($method, ['currency' => $currency])
            ->maximumAmount;

        return $maximumAmount ? mollie_object_to_money($maximumAmount) : null;
    }
}
