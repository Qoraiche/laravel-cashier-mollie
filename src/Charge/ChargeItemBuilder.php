<?php

namespace Cashier\Mollie\Charge;

use Illuminate\Database\Eloquent\Model;
use Money\Money;

class ChargeItemBuilder
{
    protected Model $owner;

    protected Money $unitPrice;

    protected string $description;

    protected int $quantity = 1;

    protected float $taxPercentage;

    public function __construct(Model $owner)
    {
        $this->owner = $owner;
        $this->taxPercentage = $owner->taxPercentage();
    }

    public function unitPrice(Money $unitPrice): ChargeItemBuilder
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function description(string $description): ChargeItemBuilder
    {
        $this->description = $description;

        return $this;
    }

    public function taxPercentage(float $taxPercentage): ChargeItemBuilder
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function quantity(int $quantity): ChargeItemBuilder
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function make(): ChargeItem
    {
        return new ChargeItem(
            $this->owner,
            $this->unitPrice,
            $this->description,
            $this->quantity,
            $this->taxPercentage
        );
    }
}
