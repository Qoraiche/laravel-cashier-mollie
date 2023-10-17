<?php

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class OrderPaymentFailed
{
    use SerializesModels;

    /**
     * The failed order.
     *
     * @var \Laravel\Cashier\Mollie\Order\Order
     */
    public $order;

    /**
     * The paid payment.
     *
     * @var \Laravel\Cashier\Mollie\Payment
     */
    public $payment;

    /**
     * Creates a new OrderPaymentFailed event.
     *
     * @param $order
     */
    public function __construct($order, $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }
}
