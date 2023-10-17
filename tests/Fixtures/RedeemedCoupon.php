<?php

namespace Cashier\Mollie\Tests\Fixtures;

use Cashier\Mollie\Coupon\RedeemedCoupon as CashierRedeemedCoupon;

class RedeemedCoupon extends CashierRedeemedCoupon
{
    protected $table = 'redeemed_coupons';
}
