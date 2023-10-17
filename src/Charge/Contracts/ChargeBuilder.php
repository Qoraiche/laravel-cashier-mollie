<?php

namespace Cashier\Mollie\Charge\Contracts;

use Cashier\Mollie\Charge\ChargeItem;
use Cashier\Mollie\Charge\ChargeItemCollection;

interface ChargeBuilder
{
    public function addItem(ChargeItem $item): self;

    public function setItems(ChargeItemCollection $items): self;

    public function setRedirectUrl(string $redirectUrl): self;

    public function create();
}
