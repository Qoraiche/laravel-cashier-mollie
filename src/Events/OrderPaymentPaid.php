<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class OrderPaymentPaid
{
    use SerializesModels;

    /**
     * The paid order.
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
     * Creates a new OrderPaymentPaid event.
     *
     * @param $order
     * @param $payment
     */
    public function __construct($order, $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }
}
