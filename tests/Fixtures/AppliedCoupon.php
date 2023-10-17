<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Coupon\AppliedCoupon as CashierAppliedCoupon;

class AppliedCoupon extends CashierAppliedCoupon
{
    protected $table = 'applied_coupons';
}
