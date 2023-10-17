<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class OrderPaymentFailedDueToInvalidMandate
{
    use SerializesModels;

    /**
     * The failed order.
     *
     * @var \Cashier\Mollie\Order\Order
     */
    public $order;

    /**
     * Creates a new OrderPaymentFailed event.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }
}
