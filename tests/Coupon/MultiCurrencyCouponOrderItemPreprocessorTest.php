<?php

namespace Laravel\Cashier\Mollie\Tests\Coupon;

use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Laravel\Cashier\Mollie\Coupon\CouponOrderItemPreprocessor;
use Laravel\Cashier\Mollie\Exceptions\CurrencyMismatchException;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;
use Laravel\Cashier\Mollie\Subscription;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;
use Laravel\Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;
use Laravel\Cashier\Mollie\Tests\Database\Factories\SubscriptionFactory;

class MultiCurrencyCouponOrderItemPreprocessorTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    /** @test */
    public function appliesCoupon()
    {
        $this->withMockedUsdCouponRepository();

        /** @var Subscription $subscription */
        $subscription = SubscriptionFactory::new()->create();
        $item = OrderItemFactory::new()->make();
        $subscription->orderItems()->save($item);

        /** @var Subscription $subscriptionUsd */
        $subscriptionUsd = SubscriptionFactory::new()->create();
        $itemUsd = OrderItemFactory::new()->USD()->make();
        $subscriptionUsd->orderItems()->save($itemUsd);

        /** @var \Laravel\Cashier\Mollie\Coupon\Coupon $coupon */
        $usdCoupon = app()->make(CouponRepository::class)->findOrFail('usddiscount');

        $redeemedUsdCoupon = $usdCoupon->redeemFor($subscription);
        $preprocessor = new CouponOrderItemPreprocessor();

        $this->assertEquals(0, Cashier::$appliedCouponModel::count());
        $this->assertEquals(1, $redeemedUsdCoupon->times_left);

        $this->expectException(CurrencyMismatchException::class);
        $preprocessor->handle($item->toCollection());

        $redeemedUsdCoupon = $usdCoupon->redeemFor($subscriptionUsd);
        $preprocessor = new CouponOrderItemPreprocessor();

        $this->assertEquals(0, Cashier::$appliedCouponModel::count());
        $this->assertEquals(1, $redeemedUsdCoupon->times_left);

        $result = $preprocessor->handle($itemUsd->toCollection());

        $this->assertEquals(1, Cashier::$appliedCouponModel::count());
        $this->assertInstanceOf(OrderItemCollection::class, $result);
        $this->assertNotEquals($item->toCollection(), $result);
        $this->assertEquals(0, $redeemedUsdCoupon->refresh()->times_left);
    }
}
