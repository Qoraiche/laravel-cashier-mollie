<?php

namespace Laravel\Cashier\Mollie\Coupon\Contracts;

use Laravel\Cashier\Mollie\Coupon\Coupon;
use Laravel\Cashier\Mollie\Exceptions\CouponNotFoundException;

interface CouponRepository
{
    /**
     * @param  string  $coupon
     * @return Coupon|null
     */
    public function find(string $coupon);

    /**
     * @param  string  $coupon
     * @return Coupon
     *
     * @throws CouponNotFoundException
     */
    public function findOrFail(string $coupon);
}
