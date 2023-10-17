<?php

namespace Cashier\Mollie\Coupon\Contracts;

use Cashier\Mollie\Coupon\Coupon;
use Cashier\Mollie\Exceptions\CouponNotFoundException;

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
