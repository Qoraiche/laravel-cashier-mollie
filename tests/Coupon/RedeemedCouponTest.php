<?php

namespace Laravel\Cashier\Mollie\Tests\Coupon;

use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Coupon\RedeemedCoupon;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;
use Laravel\Cashier\Mollie\Tests\Database\Factories\RedeemedCouponFactory;

class RedeemedCouponTest extends BaseTestCase
{
    /** @test */
    public function canBeRevoked()
    {
        $this->withPackageMigrations();

        /** @var RedeemedCoupon $redeemedCoupon */
        $redeemedCoupon = RedeemedCouponFactory::new()->create(['times_left' => 5]);

        $this->assertEquals(5, $redeemedCoupon->times_left);
        $this->assertTrue($redeemedCoupon->isActive());

        $redeemedCoupon = $redeemedCoupon->revoke();

        $this->assertEquals(0, $redeemedCoupon->times_left);
        $this->assertFalse($redeemedCoupon->isActive());
    }
}
