<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMollieMandate as Contract;
use Mollie\Api\Resources\Mandate;

class GetMollieMandate extends BaseMollieInteraction implements Contract
{
    public function execute(string $customerId, string $mandateId): Mandate
    {
        return $this->mollie->mandates()->getForId($customerId, $mandateId);
    }
}
