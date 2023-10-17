<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\UpdateMolliePayment as Contract;
use Mollie\Api\Resources\Payment;

class UpdateMolliePayment extends BaseMollieInteraction implements Contract
{
    public function execute(Payment $dirtyPayment): Payment
    {
        return $dirtyPayment->update();
    }
}
