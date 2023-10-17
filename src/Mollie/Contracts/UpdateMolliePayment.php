<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Payment;

interface UpdateMolliePayment
{
    public function execute(Payment $dirtyPayment): Payment;
}
