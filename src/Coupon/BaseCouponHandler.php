<?php

namespace Laravel\Cashier\Mollie\Coupon;

use Illuminate\Support\Arr;
use Laravel\Cashier\Mollie\Cashier;
use Laravel\Cashier\Mollie\Coupon\Contracts\AcceptsCoupons;
use Laravel\Cashier\Mollie\Coupon\Contracts\CouponHandler;
use Laravel\Cashier\Mollie\Events\CouponApplied;
use Laravel\Cashier\Mollie\Exceptions\CouponException;
use Laravel\Cashier\Mollie\Order\OrderItemCollection;

abstract class BaseCouponHandler implements CouponHandler
{
    /** @var \Laravel\Cashier\Mollie\Coupon\AppliedCoupon */
    protected $appliedCoupon;

    /** @var array */
    protected $context = [];

    /**
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    abstract public function getDiscountOrderItems(OrderItemCollection $items);

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\Coupon  $coupon
     * @param  \Laravel\Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     * @return bool
     *
     * @throws \Throwable|CouponException
     */
    public function validate(Coupon $coupon, AcceptsCoupons $model)
    {
        $this->validateOwnersFirstUse($coupon, $model);

        return true;
    }

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\RedeemedCoupon  $redeemedCoupon
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(RedeemedCoupon $redeemedCoupon, OrderItemCollection $items)
    {
        $this->markApplied($redeemedCoupon);

        return $this->apply($redeemedCoupon, $items);
    }

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\RedeemedCoupon  $redeemedCoupon
     * @param  \Laravel\Cashier\Mollie\Order\OrderItemCollection  $items
     * @return \Laravel\Cashier\Mollie\Order\OrderItemCollection
     */
    public function apply(RedeemedCoupon $redeemedCoupon, OrderItemCollection $items)
    {
        return $items->concat(
            $this->getDiscountOrderItems($items)->save()
        );
    }

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\Coupon  $coupon
     * @param  \Laravel\Cashier\Mollie\Coupon\Contracts\AcceptsCoupons  $model
     *
     * @throws \Throwable
     * @throws \Laravel\Cashier\Mollie\Exceptions\CouponException
     */
    public function validateOwnersFirstUse(Coupon $coupon, AcceptsCoupons $model)
    {
        $exists = Cashier::$redeemedCouponModel::whereName($coupon->name())
                ->whereOwnerType($model->ownerType())
                ->whereOwnerId($model->ownerId())
                ->count() > 0;

        throw_if($exists, new CouponException('You have already used this coupon.'));
    }

    /**
     * @param  \Laravel\Cashier\Mollie\Coupon\RedeemedCoupon  $redeemedCoupon
     * @return \Laravel\Cashier\Mollie\Coupon\AppliedCoupon
     */
    public function markApplied(RedeemedCoupon $redeemedCoupon)
    {
        $appliedCoupon = $this->appliedCoupon = Cashier::$appliedCouponModel::create([
            'redeemed_coupon_id' => $redeemedCoupon->id,
            'model_type' => $redeemedCoupon->model_type,
            'model_id' => $redeemedCoupon->model_id,
        ]);

        $redeemedCoupon->markApplied();

        event(new CouponApplied($redeemedCoupon, $appliedCoupon));

        return $appliedCoupon;
    }

    /**
     * Create and return an un-saved OrderItem instance. If a coupon has been applied,
     * the order item will be tied to the coupon.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model|\Laravel\Cashier\Mollie\Order\OrderItem
     */
    protected function makeOrderItem(array $data)
    {
        if ($this->appliedCoupon) {
            return $this->appliedCoupon->orderItems()->make($data);
        }

        return Cashier::$orderItemModel::make($data);
    }

    /**
     * Get an item from the context using "dot" notation.
     *
     * @param $key
     * @param  null  $default
     * @return mixed
     */
    protected function context($key, $default = null)
    {
        return Arr::get($this->context, $key, $default);
    }

    /**
     * @param  array  $context
     * @return $this
     */
    public function withContext(array $context)
    {
        $this->context = $context;

        return $this;
    }
}
