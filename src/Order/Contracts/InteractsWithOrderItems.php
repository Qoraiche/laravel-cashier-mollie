<?php

namespace Cashier\Mollie\Order\Contracts;

use Cashier\Mollie\Order\OrderItem;

interface InteractsWithOrderItems
{
    /**
     * Called right before processing the order item into an order.
     *
     * @param  OrderItem  $item
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public static function preprocessOrderItem(OrderItem $item);

    /**
     * Called after processing the order item into an order.
     *
     * @param  OrderItem  $item
     * @return OrderItem The order item that's being processed
     */
    public static function processOrderItem(OrderItem $item);

    /**
     * Handle a failed payment.
     *
     * @param  OrderItem  $item
     * @return void
     */
    public static function handlePaymentFailed(OrderItem $item);

    /**
     * Handle a paid payment.
     *
     * @param  OrderItem  $item
     * @return void
     */
    public static function handlePaymentPaid(OrderItem $item);
}
