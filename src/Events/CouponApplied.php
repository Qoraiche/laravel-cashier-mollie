<?php

namespace Cashier\Mollie\Events;

use Illuminate\Queue\SerializesModels;
use Cashier\Mollie\Coupon\AppliedCoupon;
use Cashier\Mollie\Coupon\RedeemedCoupon;

class CouponApplied
{
    use SerializesModels;

    /**
     * @var \Cashier\Mollie\Coupon\RedeemedCoupon
     */
    public $redeemedCoupon;

    /**
     * @var \Cashier\Mollie\Coupon\AppliedCoupon
     */
    public $appliedCoupon;

    public function __construct(RedeemedCoupon $redeemedCoupon, AppliedCoupon $appliedCoupon)
    {
        $this->redeemedCoupon = $redeemedCoupon;
        $this->appliedCoupon = $appliedCoupon;
    }
}
