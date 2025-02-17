<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Payment;

interface GetMolliePayment
{
    public function execute(string $id, array $parameters = []): Payment;
}
