<?php

namespace Cashier\Mollie;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Cashier\Mollie\Order\OrderInvoiceSubscriber;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        OrderInvoiceSubscriber::class,
    ];
}
