<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Order\Order;

class OrderProcessed
{
    use SerializesModels;

    /**
     * The processed order.
     *
     * @var Order
     */
    public $order;

    /**
     * OrderProcessed constructor.
     *
     * @param  \Cashier\Mollie\Order\Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
