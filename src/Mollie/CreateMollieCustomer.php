<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\CreateMollieCustomer as Contract;
use Mollie\Api\Resources\Customer;

class CreateMollieCustomer extends BaseMollieInteraction implements Contract
{
    public function execute(array $payload): Customer
    {
        return $this->mollie->customers()->create($payload);
    }
}
