<?php

namespace Cashier\Mollie\Tests\SubscriptionBuilder;

use Cashier\Mollie\Coupon\BaseCouponHandler;
use Cashier\Mollie\Coupon\Contracts\AcceptsCoupons;
use Cashier\Mollie\Coupon\Coupon;
use Cashier\Mollie\Exceptions\CouponException;
use Cashier\Mollie\Order\OrderItemCollection;

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
