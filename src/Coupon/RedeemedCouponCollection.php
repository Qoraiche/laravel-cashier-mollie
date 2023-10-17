<?php

namespace Laravel\Cashier\Mollie\Coupon;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Cashier\Mollie\Order\OrderItem;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;

class RedeemedCouponCollection extends Collection
{
    public function applyTo(OrderItem $item)
    {
        return $this->reduce(
            function (OrderItemCollection $carry, RedeemedCoupon $coupon) {
                return $coupon->applyTo($carry);
            },
            $item->toCollection()
        );
    }
}
