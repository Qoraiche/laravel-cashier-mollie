<?php

namespace Cashier\Mollie\Tests\Coupon;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Cashier\Mollie\Coupon\CouponOrderItemPreprocessor;
use Cashier\Mollie\Exceptions\CurrencyMismatchException;
use Cashier\Mollie\Order\OrderItemCollection;
use Cashier\Mollie\Subscription;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;
use Cashier\Mollie\Tests\Database\Factories\SubscriptionFactory;

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

        /** @var \Cashier\Mollie\Coupon\Coupon $coupon */
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
