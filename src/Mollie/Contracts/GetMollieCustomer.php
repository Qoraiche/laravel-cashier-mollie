<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie\Contracts;

use Mollie\Api\Resources\Customer;

interface GetMollieCustomer
{
    public function execute(string $id): Customer;
}
