<?php

namespace Laravel\Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Mollie\Coupon\AppliedCoupon;
use Laravel\Cashier\Mollie\Coupon\RedeemedCoupon;

class CouponApplied
{
    use SerializesModels;

    /**
     * @var \Laravel\Cashier\Mollie\Coupon\RedeemedCoupon
     */
    public $redeemedCoupon;

    /**
     * @var \Laravel\Cashier\Mollie\Coupon\AppliedCoupon
     */
    public $appliedCoupon;

    public function __construct(RedeemedCoupon $redeemedCoupon, AppliedCoupon $appliedCoupon)
    {
        $this->redeemedCoupon = $redeemedCoupon;
        $this->appliedCoupon = $appliedCoupon;
    }
}
