<?php

declare(strict_types=1);

namespace Cashier\Mollie\Refunds;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\RefundFailed;
use Cashier\Mollie\Events\RefundProcessed;
use Cashier\Mollie\Order\Order;
use Cashier\Mollie\Traits\HasOwner;
use Mollie\Api\Types\RefundStatus;

/**
 * @property int id
 * @property string mollie_refund_id
 * @property string mollie_refund_status
 * @property string owner_type
 * @property int owner_id
 * @property int|null original_order_item_id
 * @property int|null original_order_id
 * @property int|null order_id
 * @property \Cashier\Mollie\Refunds\RefundItemCollection items
 * @property Order order
 * @property Order originalOrder
 */
class Refund extends Model
{
    use HasOwner;

    protected $guarded = [];

    protected $casts = [
        'total' => 'int',
    ];

    /**
     * Create a new Refund Collection instance.
     *
     * @param  array  $models
     * @return \Cashier\Mollie\Refunds\RefundCollection
     */
    public function newCollection(array $models = [])
    {
        return new RefundCollection($models);
    }

    /**
     * Scope the query to only include unprocessed refunds.
     *
     * @param $query
     * @return Builder
     */
    public function scopeWhereUnprocessed(Builder $query)
    {
        return $query->where('mollie_refund_status', RefundStatus::STATUS_PENDING);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Cashier::$refundItemModel);
    }

    public function originalOrder(): HasOne
    {
        return $this->hasOne(Cashier::$orderModel, 'id', 'original_order_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Cashier::$orderModel, 'id', 'order_id');
    }

    public function handleProcessed(): self
    {
        $refundItems = $this->items;

        DB::transaction(function () use ($refundItems) {
            $orderItems = $refundItems->toNewOrderItemCollection()->save();
            $order = Cashier::$orderModel::createProcessedFromItems($orderItems);

            $this->order_id = $order->id;
            $this->mollie_refund_status = RefundStatus::STATUS_REFUNDED;

            $this->save();

            $refundItems->each(function (RefundItem $refundItem) {
                $originalOrderItem = $refundItem->originalOrderItem;

                if( $originalOrderItem && method_exists($originalOrderItem, 'handlePaymentRefunded') )
                {
                    $originalOrderItem->handlePaymentRefunded($refundItem);
                }
            });

            $this->originalOrder->increment('amount_refunded', (int) $refundItems->getTotal()->getAmount());
        });

        event(new RefundProcessed($this));

        return $this;
    }

    public function handleFailed(): self
    {
        $refundItems = $this->items;

        DB::transaction(function () use ($refundItems) {
            $this->mollie_refund_status = RefundStatus::STATUS_FAILED;

            $this->save();

            $refundItems->each(function (RefundItem $refundItem) {
                $refundItem->originalOrderItem->handlePaymentRefundFailed($refundItem);
            });
        });

        event(new RefundFailed($this));

        return $this;
    }
}
