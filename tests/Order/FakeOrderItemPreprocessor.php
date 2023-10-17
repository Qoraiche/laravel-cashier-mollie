<?php

namespace Laravel\Cashier\Mollie\Tests\Order;

use Illuminate\Support\Arr;
use Laravel\Cashier\Mollie\Order\BaseOrderItemPreprocessor;
use Laravel\Cashier\Mollie\Order\OrderItem;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;

class FakeOrderItemPreprocessor extends BaseOrderItemPreprocessor
{
    protected $items = [];

    protected $result;

    /**
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(OrderItemCollection $items)
    {
        $this->items[] = $items;

        return $this->result ?: $items;
    }

    public function withResult(OrderItemCollection $mockResult)
    {
        $this->result = $mockResult;

        return $this;
    }

    public function assertOrderItemHandled(OrderItem $item)
    {
        BaseTestCase::assertContains($item, Arr::flatten($this->items), "OrderItem `{$item->description}` was not handled");
    }
}
