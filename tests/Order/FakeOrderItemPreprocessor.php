<?php

namespace Cashier\Mollie\Tests\Order;

use Illuminate\Support\Arr;
use Cashier\Mollie\Order\BaseOrderItemPreprocessor;
use Cashier\Mollie\Order\OrderItem;
use Cashier\Mollie\Order\OrderItemCollection;
use Cashier\Mollie\Tests\BaseTestCase;

class FakeOrderItemPreprocessor extends BaseOrderItemPreprocessor
{
    protected $items = [];

    protected $result;

    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
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
