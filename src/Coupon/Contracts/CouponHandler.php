<?php

namespace Cashier\Mollie\Coupon\Contracts;

use Cashier\Mollie\Coupon\Coupon;
use Cashier\Mollie\Coupon\RedeemedCoupon;
use Cashier\Mollie\Exceptions\CouponException;
use Cashier\Mollie\Order\OrderItemCollection;

interface CouponHandler
{
    /**
     * @param  array  $context
     * @return \Cashier\Mollie\Coupon\Contracts\CouponHandler
     */
    public function withContext(array $context);

    /**
     * @param  \Cashier\Mollie\Coupon\Coupon  $coupon
     * @param  \Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     * @return bool
     *
     * @throws \Throwable|CouponException
     */
    public function validate(Coupon $coupon, AcceptsCoupons $model);

    /**
     * Apply the coupon to the OrderItemCollection
     *
     * @param  \Cashier\Mollie\Coupon\RedeemedCoupon  $redeemedCoupon
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(RedeemedCoupon $redeemedCoupon, OrderItemCollection $items);

    /**
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function getDiscountOrderItems(OrderItemCollection $items);
}
