<?php

namespace Laravel\Cashier\Mollie\Charge;

use Laravel\Cashier\Mollie\Charge\Contracts\ChargeBuilder;

trait ManagesCharges
{
    public function newCharge(): ChargeBuilder
    {
        if (! $this->validateMollieMandate()) {
            return $this->newFirstPaymentChargeThroughCheckout();
        }

        return $this->newMandatedCharge();
    }

    public function newFirstPaymentChargeThroughCheckout(): FirstPaymentChargeBuilder
    {
        return new FirstPaymentChargeBuilder($this);
    }

    public function newMandatedCharge(): MandatedChargeBuilder
    {
        return new MandatedChargeBuilder($this);
    }
}
