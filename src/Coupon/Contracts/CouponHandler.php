<?php

namespace Laravel\Cashier\Mollie\Coupon\Contracts;

use Laravel\Cashier\Mollie\Coupon\Coupon;
use Laravel\Cashier\Mollie\Coupon\RedeemedCoupon;
use Laravel\Cashier\Mollie\Exceptions\CouponException;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;

interface CouponHandler
{
    /**
     * @param  array  $context
     * @return \Laravel\Cashier\Mollie\Coupon\Contracts\CouponHandler
     */
    public function withContext(array $context);

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\Coupon  $coupon
     * @param  \Laravel\Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     * @return bool
     *
     * @throws \Throwable|CouponException
     */
    public function validate(Coupon $coupon, AcceptsCoupons $model);

    /**
     * Apply the coupon to the OrderItemCollection
     *
     * @param  \Laravel\Cashier\Mollie\Coupon\RedeemedCoupon  $redeemedCoupon
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(RedeemedCoupon $redeemedCoupon, OrderItemCollection $items);

    /**
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function getDiscountOrderItems(OrderItemCollection $items);
}
