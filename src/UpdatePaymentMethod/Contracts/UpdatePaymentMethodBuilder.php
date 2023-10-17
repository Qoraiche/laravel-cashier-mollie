<?php

namespace Cashier\Mollie\UpdatePaymentMethod\Contracts;

interface UpdatePaymentMethodBuilder
{
    /**
     * Update payment method.
     *
     * @return \Cashier\Mollie\Http\RedirectToCheckoutResponse
     */
    public function create();
}
