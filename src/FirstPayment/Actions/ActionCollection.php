<?php

namespace Cashier\Mollie\FirstPayment\Actions;

use Illuminate\Support\Collection;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Exceptions\CurrencyMismatchException;
use Cashier\Mollie\Order\OrderItemCollection;
use Money\Currency;
use Money\Money;

class ActionCollection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->validate();
    }

    protected function validate()
    {
        if ($this->isNotEmpty()) {
            $firstAmount = $this->first()->getTotal();
            $this->each(function (BaseAction $item) use ($firstAmount) {
                if (! $item->getTotal()->isSameCurrency($firstAmount)) {
                    throw new CurrencyMismatchException('All actions must be in the same currency');
                }
            });
        }
    }

    /**
     * @return \Money\Money
     */
    public function total()
    {
        $total = new Money(0, new Currency($this->getCurrency()));

        $this->each(function (BaseAction $item) use (&$total) {
            $total = $total->add($item->getTotal());
        });

        return $total;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        if ($this->isNotEmpty()) {
            return $this->first()->getTotal()->getCurrency()->getCode();
        }

        return strtoupper(Cashier::usesCurrency());
    }

    /**
     * @return array
     */
    public function toPlainArray()
    {
        $payload = [];
        foreach ($this->items as $item) {
            /** @var \Cashier\Mollie\FirstPayment\Actions\BaseAction $item */
            $itemPayload = $item->getPayload();

            if (! empty($itemPayload)) {
                $payload[] = $itemPayload;
            }
        }

        return $payload;
    }

    /**
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function processedOrderItems()
    {
        $orderItems = new OrderItemCollection;

        /** @var \Cashier\Mollie\FirstPayment\Actions\BaseAction $action */
        foreach ($this->items as $action) {
            $orderItems = $orderItems->concat($action->makeProcessedOrderItems());
        }

        return $orderItems;
    }
}
