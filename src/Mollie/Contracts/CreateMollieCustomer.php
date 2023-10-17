<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Customer;

interface CreateMollieCustomer
{
    public function execute(array $payload): Customer;
}
