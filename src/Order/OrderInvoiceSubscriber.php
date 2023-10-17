<?php

namespace Laravel\Cashier\Mollie\Order;

use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Mollie\Events\FirstPaymentPaid;
use Laravel\Cashier\Mollie\Events\OrderInvoiceAvailable;
use Laravel\Cashier\Mollie\Events\OrderPaymentPaid;

class OrderInvoiceSubscriber
{
    /**
     * @param  FirstPaymentPaid  $event
     */
    public function handleFirstPaymentPaid($event)
    {
        Event::dispatch(new OrderInvoiceAvailable($event->order));
    }

    /**
     * @param  OrderPaymentPaid  $event
     */
    public function handleOrderPaymentPaid($event)
    {
        Event::dispatch(new OrderInvoiceAvailable($event->order));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            OrderPaymentPaid::class,
            self::class.'@handleOrderPaymentPaid'
        );

        $events->listen(
            FirstPaymentPaid::class,
            self::class.'@handleFirstPaymentPaid'
        );
    }
}
