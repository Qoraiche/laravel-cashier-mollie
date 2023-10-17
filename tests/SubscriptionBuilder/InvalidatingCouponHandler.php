<?php

namespace Laravel\Cashier\Mollie\Tests\SubscriptionBuilder;

use Laravel\Cashier\Mollie\Coupon\BaseCouponHandler;
use Laravel\Cashier\Mollie\Coupon\Contracts\AcceptsCoupons;
use Laravel\Cashier\Mollie\Coupon\Coupon;
use Laravel\Cashier\Mollie\Exceptions\CouponException;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;

class InvalidatingCouponHandler extends BaseCouponHandler
{
    public function validate(Coupon $coupon, AcceptsCoupons $model)
    {
        throw new CouponException('This exception should be thrown');
    }

    public function getDiscountOrderItems(OrderItemCollection $items)
    {
        return $items;
    }
}
