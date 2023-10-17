<?php

namespace Laravel\Cashier\Mollie\Tests\Order;

use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Events\FirstPaymentPaid;
use Laravel\Cashier\Mollie\Events\OrderInvoiceAvailable;
use Laravel\Cashier\Mollie\Events\OrderPaymentPaid;
use Laravel\Cashier\Mollie\Order\OrderInvoiceSubscriber;
use Laravel\Cashier\Mollie\Payment as CashierPayment;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;
use Laravel\Cashier\Mollie\Tests\Database\Factories\OrderFactory;
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
