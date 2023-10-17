<?php

namespace Cashier\Mollie\FirstPayment\Actions;

use Illuminate\Database\Eloquent\Model;
use Cashier\Mollie\Coupon\Coupon;
use Cashier\Mollie\Order\OrderItemCollection;
use Money\Currency;
use Money\Money;

class ApplySubscriptionCouponToPayment extends BaseNullAction
{
    /**
     * @var \Cashier\Mollie\Coupon\Coupon
     */
    protected $coupon;

    /**
     * The coupon's (discount) OrderItems
     *
     * @var \Cashier\Mollie\Order\OrderItemCollection
     */
    protected $orderItems;

    /**
     * ApplySubscriptionCouponToPayment constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @param  \Cashier\Mollie\Coupon\Coupon  $coupon
     * @param  \Cashier\Mollie\Order\OrderItemCollection  $orderItems
     */
    public function __construct(Model $owner, Coupon $coupon, OrderItemCollection $orderItems)
    {
        $this->owner = $owner;
        $this->coupon = $coupon;
        $this->currency = $coupon->context()['discount']['currency'] ?? null;
        $this->orderItems = $this->coupon->handler()->getDiscountOrderItems($orderItems);
    }

    /**
     * @return \Money\Money
     */
    public function getSubtotal()
    {
        return $this->toMoney($this->orderItems->sum('subtotal'));
    }

    /**
     * @return \Money\Money
     */
    public function getTax()
    {
        return $this->toMoney($this->orderItems->sum('tax'));
    }

    /**
     * @param  int  $value
     * @return \Money\Money
     */
    protected function toMoney($value = 0)
    {
        return new Money($value, new Currency($this->getCurrency()));
    }
}
