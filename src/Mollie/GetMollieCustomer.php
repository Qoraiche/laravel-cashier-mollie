<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMollieCustomer as Contract;
use Mollie\Api\Resources\Customer;

class GetMollieCustomer extends BaseMollieInteraction implements Contract
{
    public function execute(string $id): Customer
    {
        return $this->mollie->customers()->get($id);
    }
}
