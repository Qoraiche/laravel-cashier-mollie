<?php

namespace Cashier\Mollie\Http;

use Illuminate\Http\RedirectResponse;
use Mollie\Api\Resources\Payment;

class RedirectToCheckoutResponse extends RedirectResponse
{
    /** @var \Mollie\Api\Resources\Payment */
    protected Payment $payment;

    /**
     * @param  \Mollie\Api\Resources\Payment  $payment
     * @param  array  $context
     * @return \Cashier\Mollie\Http\RedirectToCheckoutResponse
     */
    public static function forPayment(Payment $payment, array $context = [])
    {
        $response = new static($payment->getCheckoutUrl());

        return $response
            ->setPayment($payment);
    }

    /**
     * @return \Mollie\Api\Resources\Payment
     */
    public function payment()
    {
        return $this->payment;
    }

    /**
     * @param  \Mollie\Api\Resources\Payment  $payment
     * @return \Cashier\Mollie\Http\RedirectToCheckoutResponse
     */
    protected function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }
}
