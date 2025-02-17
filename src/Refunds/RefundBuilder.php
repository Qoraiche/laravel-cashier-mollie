<?php

declare(strict_types=1);

namespace Cashier\Mollie\Refunds;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\RefundInitiated;
use Cashier\Mollie\Mollie\Contracts\CreateMollieRefund;
use Cashier\Mollie\Order\Order;
use Cashier\Mollie\Order\OrderItem;
use Cashier\Mollie\Order\OrderItemCollection;
use LogicException;
use Mollie\Api\Types\PaymentStatus;

class RefundBuilder
{
    /**
     * @var \Cashier\Mollie\Order\Order
     */
    protected Order $order;

    /**
     * @var \Cashier\Mollie\Refunds\RefundItemCollection
     */
    protected RefundItemCollection $items;

    /**
     * @var CreateMollieRefund
     */
    protected CreateMollieRefund $createMollieRefund;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->items = new RefundItemCollection;
        $this->createMollieRefund = app()->make(CreateMollieRefund::class);
    }

    public static function forOrder(Order $order): self
    {
        static::guardOrderIsPaid($order);

        return new static($order);
    }

    public static function forWholeOrder(Order $order): self
    {
        static::guardOrderIsPaid($order);
        $refund = new static($order);

        return $refund->addItems(RefundItemCollection::makeFromOrderItemCollection($order->items));
    }

    public function addItem(RefundItem $item): self
    {
        $this->items->add($item);

        return $this;
    }

    public function addItems(RefundItemCollection $items): self
    {
        $this->items = $this->items->concat($items);

        return $this;
    }

    public function addItemFromOrderItem(OrderItem $orderItem, array $overrides = []): self
    {
        return $this->addItem(Cashier::$refundItemModel::makeFromOrderItem($orderItem, $overrides));
    }

    public function addItemsFromOrderItemCollection(OrderItemCollection $orderItems, array $overrides = []): self
    {
        return $this->addItems(RefundItemCollection::makeFromOrderItemCollection($orderItems, $overrides));
    }

    protected static function guardOrderIsPaid(Order $order)
    {
        throw_unless(
            $order->mollie_payment_status === PaymentStatus::STATUS_PAID,
            new LogicException('Only paid orders can be refunded')
        );
    }

    public function create(): Refund
    {
        $currency = $this->order->getCurrency();

        $mollieRefund = $this->createMollieRefund->execute($this->order->mollie_payment_id, [
            'amount' => [
                'value' => money_to_decimal($this->items->getTotal()),
                'currency' => $currency,
            ],
        ]);

        $refundRecord = Cashier::$refundModel::create([
            'owner_type' => $this->order->owner_type,
            'owner_id' => $this->order->owner_id,
            'original_order_id' => $this->order->getKey(),
            'total' => $this->items->getTotal()->getAmount(),
            'currency' => $currency,
            'mollie_refund_id' => $mollieRefund->id,
            'mollie_refund_status' => $mollieRefund->status,
        ]);

        $refundRecord->items()->saveMany($this->items);

        event(new RefundInitiated($refundRecord));

        return $refundRecord;
    }
}
