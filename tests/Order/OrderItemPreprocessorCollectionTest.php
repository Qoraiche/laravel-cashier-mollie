<?php

namespace Cashier\Mollie\Tests\Order;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Order\OrderItemCollection;
use Cashier\Mollie\Order\OrderItemPreprocessorCollection;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;

class OrderItemPreprocessorCollectionTest extends BaseTestCase
{
    /** @test */
    public function handlesOrderItem()
    {
        $fakePreprocessor = $this->getFakePreprocessor(OrderItemFactory::new()->times(2)->make());
        $preprocessors = new OrderItemPreprocessorCollection([$fakePreprocessor]);
        $item = OrderItemFactory::new()->make();

        $result = $preprocessors->handle($item);

        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertEquals(2, $result->count());
        $fakePreprocessor->assertOrderItemHandled($item);
    }

    /** @test */
    public function invokesPreprocessorsOneByOne()
    {
        $preprocessor1 = $this->getFakePreprocessor(OrderItemFactory::new()->times(1)->make());
        $preprocessor2 = $this->getFakePreprocessor(OrderItemFactory::new()->times(2)->make());
        $preprocessors = new OrderItemPreprocessorCollection([$preprocessor1, $preprocessor2]);
        $item = OrderItemFactory::new()->make();

        $result = $preprocessors->handle($item);

        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertEquals(2, $result->count());
    }

    /** @test */
    public function handlesEmptyPreprocessorCollection()
    {
        $preprocessors = new OrderItemPreprocessorCollection;
        $item = OrderItemFactory::new()->make();

        $result = $preprocessors->handle($item);

        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertTrue($result->first()->is($item));
    }

    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Tests\Order\FakeOrderItemPreprocessor
     */
    protected function getFakePreprocessor(OrderItemCollection $items)
    {
        return (new FakeOrderItemPreprocessor)->withResult($items);
    }
}
