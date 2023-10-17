<?php

namespace Cashier\Mollie\FirstPayment\Actions;

use Illuminate\Database\Eloquent\Model;
use Cashier\Mollie\Order\OrderItemCollection;

abstract class BaseNullAction extends BaseAction
{
    /**
     * Rebuild the Action from a payload.
     *
     * @param  array  $payload
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     */
    public static function createFromPayload(array $payload, Model $owner)
    {
        //
    }

    public function getPayload()
    {
        //
    }

    /**
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function makeProcessedOrderItems()
    {
        return new OrderItemCollection;
    }

    /**
     * Execute this action and return the created OrderItem or OrderItemCollection.
     *
     * @return \Cashier\Mollie\Order\OrderItem|\Cashier\Mollie\Order\OrderItemCollection
     */
    public function execute()
    {
        return new OrderItemCollection;
    }
}
