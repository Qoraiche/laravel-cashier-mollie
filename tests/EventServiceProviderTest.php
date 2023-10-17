<?php

namespace Cashier\Mollie\Tests;

use Illuminate\Support\Facades\Event;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\OrderInvoiceAvailable;
use Cashier\Mollie\Events\OrderPaymentPaid;
use Cashier\Mollie\Tests\Database\Factories\OrderFactory;

class EventServiceProviderTest extends BaseTestCase
{
    /** @test */
    public function itIsWiredUpAndFiring()
    {
        Event::fake(OrderInvoiceAvailable::class);

        $event = new OrderPaymentPaid(
            OrderFactory::new()->make(),
            $this->mock(Cashier::$paymentModel)
        );

        Event::dispatch($event);

        Event::assertDispatched(OrderInvoiceAvailable::class, function ($e) use ($event) {
            return $e->order === $event->order;
        });
    }
}
