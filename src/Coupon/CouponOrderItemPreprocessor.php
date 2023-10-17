<?php

namespace Cashier\Mollie\Coupon;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Order\BaseOrderItemPreprocessor;
use Cashier\Mollie\Order\OrderItem;
use Cashier\Mollie\Order\OrderItemCollection;

class CouponOrderItemPreprocessor extends BaseOrderItemPreprocessor
{
    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(OrderItemCollection $items)
    {
        $result = new OrderItemCollection;

        $items->each(function (OrderItem $item) use (&$result) {
            if ($item->orderableIsSet()) {
                $coupons = $this->getActiveCoupons($item->orderable_type, $item->orderable_id);
                $result = $result->concat($coupons->applyTo($item));
            } else {
                $result->push($item);
            }
        });

        return $result;
    }

    /**
     * @param $modelType
     * @param $modelId
     * @return mixed
     */
    protected function getActiveCoupons($modelType, $modelId)
    {
        return Cashier::$redeemedCouponModel::whereModel($modelType, $modelId)->active()->get();
    }
}
