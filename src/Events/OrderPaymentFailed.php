<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class OrderPaymentFailed
{
    use SerializesModels;

    /**
     * The failed order.
     *
     * @var \Cashier\Mollie\Order\Order
     */
    public $order;

    /**
     * The paid payment.
     *
     * @var \Cashier\Mollie\Payment
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
