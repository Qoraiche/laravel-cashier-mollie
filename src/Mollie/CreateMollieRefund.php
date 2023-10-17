<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\CreateMollieRefund as Contract;
use Cashier\Mollie\Mollie\Contracts\GetMolliePayment;
use Mollie\Api\Resources\Refund;
use Mollie\Laravel\Wrappers\MollieApiWrapper as Mollie;

class CreateMollieRefund extends BaseMollieInteraction implements Contract
{
    /**
     * @var \Cashier\Mollie\Mollie\Contracts\GetMolliePayment
     */
    protected GetMolliePayment $getMolliePayment;

    public function __construct(Mollie $mollie, GetMolliePayment $getMolliePayment)
    {
        parent::__construct($mollie);

        $this->getMolliePayment = $getMolliePayment;
    }

    public function execute(string $paymentId, array $payload): Refund
    {
        $payment = $this->getMolliePayment->execute($paymentId);

        /** @var Refund $refund */
        $refund = $payment->refund($payload);

        return $refund;
    }
}
