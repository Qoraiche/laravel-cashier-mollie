<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Money\Money;

interface GetMollieMethodMaximumAmount
{
    public function execute(string $method, string $currency): ?Money;
}
