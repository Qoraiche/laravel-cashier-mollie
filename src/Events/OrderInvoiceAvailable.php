<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class OrderInvoiceAvailable
{
    use SerializesModels;

    /**
     * The created order.
     *
     * @var \Cashier\Mollie\Order\Order
     */
    public $order;

    /**
     * Creates a new OrderInvoiceAvailable event.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }
}
