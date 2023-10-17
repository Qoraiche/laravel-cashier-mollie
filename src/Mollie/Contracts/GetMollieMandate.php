<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Mandate;

interface GetMollieMandate
{
    public function execute(string $customerId, string $mandateId): Mandate;
}
