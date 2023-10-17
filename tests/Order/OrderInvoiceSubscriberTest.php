<?php

namespace Cashier\Mollie\Tests\Order;

use Illuminate\Support\Facades\Event;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\FirstPaymentPaid;
use Cashier\Mollie\Events\OrderInvoiceAvailable;
use Cashier\Mollie\Events\OrderPaymentPaid;
use Cashier\Mollie\Order\OrderInvoiceSubscriber;
use Cashier\Mollie\Payment as CashierPayment;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\OrderFactory;
use Mollie\Api\Resources\Payment;

class OrderInvoiceSubscriberTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriber = new OrderInvoiceSubscriber;
    }

    /** @test */
    public function itHandlesTheFirstPaymentPaidEvent()
    {
        $this->assertItHandlesEvent(
            new FirstPaymentPaid($this->mock(Payment::class), $this->order()),
            'handleFirstPaymentPaid'
        );
    }

    /** @test */
    public function itHandlesTheOrderPaymentPaidEvent()
    {
        $this->assertItHandlesEvent(
            new OrderPaymentPaid($this->order(), $this->mock(Cashier::$paymentModel)),
            'handleOrderPaymentPaid'
        );
    }

    private function assertItHandlesEvent($event, $methodName)
    {
        Event::fake(OrderInvoiceAvailable::class);

        (new OrderInvoiceSubscriber)->$methodName($event);

        Event::assertDispatched(OrderInvoiceAvailable::class, function ($e) use ($event) {
            return $e->order === $event->order;
        });
    }

    private function order()
    {
        return OrderFactory::new()->make();
    }
}
