<?php

namespace Laravel\Cashier\Mollie\Tests\Fixtures;

use Laravel\Cashier\Mollie\Coupon\RedeemedCoupon as CashierRedeemedCoupon;

class RedeemedCoupon extends CashierRedeemedCoupon
{
    protected $table = 'redeemed_coupons';
}
