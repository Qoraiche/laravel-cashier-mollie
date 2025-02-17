<?php

namespace Cashier\Mollie\Coupon;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Coupon\Contracts\AcceptsCoupons;
use Cashier\Mollie\Coupon\Contracts\CouponHandler;
use Cashier\Mollie\Order\OrderItemCollection;

class Coupon
{
    /** @var string */
    protected $name;

    /** @var \Cashier\Mollie\Coupon\Contracts\CouponHandler */
    protected $handler;

    /** @var array */
    protected $context;

    /** @var int The number of times this coupon should be applied */
    protected $times = 1;

    /**
     * Coupon constructor.
     *
     * @param  string  $name
     * @param  \Cashier\Mollie\Coupon\Contracts\CouponHandler  $handler
     * @param  array  $context
     */
    public function __construct(string $name, CouponHandler $handler, array $context = [])
    {
        $this->name = $name;
        $this->context = $context;
        $this->handler = $handler;
        $this->handler->withContext($context);
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return \Cashier\Mollie\Coupon\Contracts\CouponHandler
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * The number of times the coupon will be applied
     *
     * @return int
     */
    public function times()
    {
        return $this->times;
    }

    /**
     * @param $times
     * @return \Cashier\Mollie\Coupon\Coupon
     *
     * @throws \LogicException|\Throwable
     */
    public function withTimes($times)
    {
        throw_if($times < 1, new \LogicException('Cannot apply coupons less than one time.'));

        $this->times = $times;

        return $this;
    }

    /**
     * @param  \Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     * @return \Cashier\Mollie\Coupon\RedeemedCoupon
     */
    public function redeemFor(AcceptsCoupons $model)
    {
        return Cashier::$redeemedCouponModel::record($this, $model);
    }

    /**
     * Check if the coupon can be applied to the model
     *
     * @param  \Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     *
     * @throws \Throwable|\Cashier\Mollie\Exceptions\CouponException
     */
    public function validateFor(AcceptsCoupons $model)
    {
        $this->handler->validate($this, $model);
    }

    public function applyTo(RedeemedCoupon $redeemedCoupon, OrderItemCollection $items)
    {
        return $this->handler->handle($redeemedCoupon, $items);
    }
}
