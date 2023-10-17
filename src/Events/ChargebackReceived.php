<?php

declare(strict_types=1);

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Payment;
use Money\Money;

class ChargebackReceived
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Payment
     */
    public Payment $payment;

    public Money $amountChargedBack;

    public function __construct(Payment $payment, Money $amountChargedBack)
    {
        $this->payment = $payment;
        $this->amountChargedBack = $amountChargedBack;
    }
}
