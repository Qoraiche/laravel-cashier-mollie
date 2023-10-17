<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMolliePayment;
use Cashier\Mollie\Mollie\Contracts\GetMollieRefund as Contract;
use Mollie\Api\Resources\Refund;
use Mollie\Laravel\Wrappers\MollieApiWrapper as Mollie;

class GetMollieRefund implements Contract
{
    /**
     * @var \Mollie\Laravel\Wrappers\MollieApiWrapper
     */
    protected Mollie $mollie;

    /**
     * @var \Cashier\Mollie\Mollie\Contracts\GetMolliePayment
     */
    protected GetMolliePayment $getMolliePayment;

    public function __construct(Mollie $mollie, GetMolliePayment $getMolliePayment)
    {
        $this->mollie = $mollie;
        $this->getMolliePayment = $getMolliePayment;
    }

    public function execute(string $paymentId, string $refundId): Refund
    {
        $payment = $this->getMolliePayment->execute($paymentId);

        return $payment->getRefund($refundId);
    }
}
