<?php

namespace Cashier\Mollie\Tests\Coupon;

use Cashier\Mollie\Coupon\ConfigCouponRepository;
use Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Cashier\Mollie\Coupon\Coupon;
use Cashier\Mollie\Exceptions\CouponNotFoundException;
use Cashier\Mollie\Tests\BaseTestCase;

class ConfigCouponRepositoryTest extends BaseTestCase
{
    /** @var ConfigCouponRepository */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $defaults = [
            'handler' => '\NonExistentHandler',
            'times' => 6,
            'context' => [
                'foo' => 'bar',
            ],
        ];
        $coupons = [
            'test-coupon' => [
                'handler' => \Cashier\Mollie\Coupon\FixedDiscountHandler::class,
                'context' => [
                    'description' => 'Welcome to '.config('app.name'),
                    'discount' => [
                        'currency' => 'EUR',
                        'value' => '5.00',
                    ],
                ],
            ],
        ];
        $this->repository = new ConfigCouponRepository($defaults, $coupons);
    }

    /** @test */
    public function ItIsContainerBound()
    {
        $repository = app()->make(CouponRepository::class);
        $this->assertInstanceOf(ConfigCouponRepository::class, $repository);
    }

    /** @test */
    public function findReturnsNullWhenNotFound()
    {
        $this->assertNull($this->repository->find('some_wrong_name'));
    }

    /** @test */
    public function findReturnsCouponWhenFound()
    {
        $this->assertInstanceOf(Coupon::class, $this->repository->find('test-coupon'));
    }

    /** @test */
    public function findOrFailCorrect()
    {
        $this->assertInstanceOf(Coupon::class, $this->repository->findOrFail('test-coupon'));
    }

    /** @test */
    public function findOrFailWrong()
    {
        $this->expectException(CouponNotFoundException::class);
        $this->repository->findOrFail('some_wrong_name');
    }

    /** @test */
    public function findOrFailIsCaseInsensitive()
    {
        $lowercaseCoupon = $this->repository->find('test-coupon');
        $uppercaseCoupon = $this->repository->find('TEST-COUPON');
        $this->assertInstanceOf(Coupon::class, $lowercaseCoupon);
        $this->assertInstanceOf(Coupon::class, $uppercaseCoupon);
        $this->assertEquals($lowercaseCoupon, $uppercaseCoupon);
    }

    /** @test */
    public function itHandlesTimesAttribute()
    {
        $coupon = $this->repository->findOrFail('test-coupon');

        $this->assertEquals(6, $coupon->times());
    }
}
