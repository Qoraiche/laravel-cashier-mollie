<?php

namespace Cashier\Mollie\Tests\Coupon;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Coupon\RedeemedCoupon;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\RedeemedCouponFactory;

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
