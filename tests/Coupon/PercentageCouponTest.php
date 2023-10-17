<?php

namespace Laravel\Cashier\Mollie\Tests\Coupon;

use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Laravel\Cashier\Mollie\Coupon\Coupon;
use Laravel\Cashier\Mollie\Coupon\CouponOrderItemPreprocessor;
use Laravel\Cashier\Mollie\Coupon\PercentageDiscountHandler;
use Laravel\Cashier\Mollie\Subscription;
use Laravel\Cashier\Mollie\Tests\BaseTestCase;
use Laravel\Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;
use Laravel\Cashier\Mollie\Tests\Database\Factories\SubscriptionFactory;

class PercentageCouponTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    /** @test */
    public function couponCalculatesTheRightPrice()
    {
        $couponHandler = new PercentageDiscountHandler;

        $context = [
            'description' => 'Percentage coupon',
            'percentage' => 20,
        ];

        $coupon = new Coupon(
            'percentage-coupon',
            $couponHandler,
            $context
        );

        $this->withMockedCouponRepository($coupon, $couponHandler, $context);

        /** @var Subscription $subscription */
        $subscription = SubscriptionFactory::new()->create();
        $item = OrderItemFactory::new()->make();
        $subscription->orderItems()->save($item);

        /** @var \Laravel\Cashier\Mollie\Coupon\Coupon $coupon */
        $coupon = app()->make(CouponRepository::class)->findOrFail('percentage-coupon');
        $redeemedCoupon = $coupon->redeemFor($subscription);
        $preprocessor = new CouponOrderItemPreprocessor();

        $result = $preprocessor->handle($item->toCollection());

        $this->assertEquals(-2952, $result[1]->unit_price);
    }
}
