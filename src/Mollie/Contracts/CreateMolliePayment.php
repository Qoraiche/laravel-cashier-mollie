<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Payment;

interface CreateMolliePayment
{
    public function execute(array $payload): Payment;
}
