<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMolliePayment as Contract;
use Mollie\Api\Resources\Payment;

class GetMolliePayment extends BaseMollieInteraction implements Contract
{
    public function execute(string $id, array $parameters = []): Payment
    {
        return $this->mollie->payments()->get($id, $parameters);
    }
}
