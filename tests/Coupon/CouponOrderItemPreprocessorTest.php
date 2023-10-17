<?php

namespace Laravel\Cashier\Mollie\Tests\Coupon;

use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Laravel\Cashier\Mollie\Coupon\CouponOrderItemPreprocessor;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;
use Laravel\Cashier\Mollie\Subscription;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;
use Laravel\Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;
use Laravel\Cashier\Mollie\Tests\Database\Factories\SubscriptionFactory;

class CouponOrderItemPreprocessorTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    /** @test */
    public function appliesCoupon()
    {
        $this->withMockedCouponRepository();

        /** @var Subscription $subscription */
        $subscription = SubscriptionFactory::new()->create();
        $item = OrderItemFactory::new()->make();
        $subscription->orderItems()->save($item);

        /** @var \Laravel\Cashier\Mollie\Coupon\Coupon $coupon */
        $coupon = app()->make(CouponRepository::class)->findOrFail('test-coupon');
        $redeemedCoupon = $coupon->redeemFor($subscription);
        $preprocessor = new CouponOrderItemPreprocessor();
        $this->assertEquals(0, Cashier::$appliedCouponModel::count());
        $this->assertEquals(1, $redeemedCoupon->times_left);

        $result = $preprocessor->handle($item->toCollection());

        $this->assertEquals(1, Cashier::$appliedCouponModel::count());
        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertNotEquals($item->toCollection(), $result);
        $this->assertEquals(0, $redeemedCoupon->refresh()->times_left);
    }

    /** @test */
    public function passesThroughWhenNoRedeemedCoupon()
    {
        $preprocessor = new CouponOrderItemPreprocessor();
        $items = OrderItemFactory::new()->times(1)->make();
        $this->assertInstanceOf(OrderItemCollection::class, $items);
        $this->assertEquals(0, Cashier::$redeemedCouponModel::count());

        $result = $preprocessor->handle($items);

        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertEquals($items, $result);
    }
}
