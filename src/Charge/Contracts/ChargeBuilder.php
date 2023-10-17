<?php

namespace Laravel\Cashier\Mollie\Charge\Contracts;

use Laravel\Cashier\Mollie\Charge\ChargeItem;
use Laravel\Cashier\Mollie\Charge\ChargeItemCollection;

interface ChargeBuilder
{
    public function addItem(ChargeItem $item): self;

    public function setItems(ChargeItemCollection $items): self;

    public function setRedirectUrl(string $redirectUrl): self;

    public function create();
}
