<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Coupon\AppliedCoupon as CashierAppliedCoupon;

class AppliedCoupon extends CashierAppliedCoupon
{
    protected $table = 'applied_coupons';
}
