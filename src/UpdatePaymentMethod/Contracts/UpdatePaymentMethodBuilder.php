<?php

namespace Laravel\Cashier\Mollie\UpdatePaymentMethod\Contracts;

interface UpdatePaymentMethodBuilder
{
    /**
     * Update payment method.
     *
     * @return \Laravel\Cashier\Mollie\Http\RedirectToCheckoutResponse
     */
    public function create();
}
