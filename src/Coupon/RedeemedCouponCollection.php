<?php

namespace Cashier\Mollie\Coupon;

use Illuminate\Database\Eloquent\Collection;
use Cashier\Mollie\Order\OrderItem;
use Cashier\Mollie\Order\OrderItemCollection;

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
