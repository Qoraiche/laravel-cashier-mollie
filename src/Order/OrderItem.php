<?php

namespace Cashier\Mollie\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Order\Contracts\InteractsWithOrderItems;
use Cashier\Mollie\Order\Contracts\InvoicableItem;
use Cashier\Mollie\Refunds\Contracts\IsRefundable;
use Cashier\Mollie\Refunds\RefundItem;
use Cashier\Mollie\Traits\FormatsAmount;
use Cashier\Mollie\Traits\HasOwner;

/**
 * @property InteractsWithOrderItems orderable
 * @property \Carbon\Carbon process_at
 * @property int quantity
 * @property string currency
 * @property int unit_price
 * @property float tax_percentage
 * @property string orderable_type
 * @property mixed orderable_id
 * @property mixed $id
 * @property Order $order
 * @property mixed $order_id
 *
 * @method static create(array $array)
 * @method static make(array $array)
 */
class OrderItem extends Model implements InvoicableItem
{
    use HasOwner;
    use FormatsAmount;
    use ConvertsToMoney;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'description_extra_lines' => 'array',
        'quantity' => 'int',
        'unit_price' => 'int',
        'tax_percentage' => 'float',
        'orderable_id' => 'int',
        'process_at' => 'datetime',
    ];

    protected $guarded = [];

    /**
     * Get the orderable model for this order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function orderable()
    {
        return $this->morphTo('orderable');
    }

    /**
     * Return the order for this order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Cashier::$orderModel);
    }

    /**
     * Get the order item total before taxes.
     *
     * @return int
     */
    public function getSubtotalAttribute()
    {
        return (int) $this->getUnitPrice()->multiply($this->quantity ?: 1)->getAmount();
    }

    /**
     * Get the order item tax money value.
     *
     * @return int
     */
    public function getTaxAttribute()
    {
        $beforeTax = $this->getSubtotal();

        return (int) $beforeTax->multiply(
            sprintf('%.6F', $this->tax_percentage / 100)
        )->getAmount();
    }

    /**
     * Get the order item total after taxes.
     *
     * @return int
     */
    public function getTotalAttribute()
    {
        $beforeTax = $this->getSubtotal();

        return (int) $beforeTax->add($this->getTax())->getAmount();
    }

    /**
     * Scope the query to only include unprocessed order items.
     *
     * @param $query
     * @param  bool  $processed
     * @return Builder
     */
    public function scopeProcessed($query, $processed = true)
    {
        if (!$processed) {
            return $query->whereNull('order_id');
        }

        return $query->whereNotNull('order_id');
    }

    /**
     * Scope the query to only include unprocessed order items.
     *
     * @param $query
     * @param  bool  $unprocessed
     * @return Builder
     */
    public function scopeUnprocessed($query, $unprocessed = true)
    {
        return $query->processed(!$unprocessed);
    }

    /**
     * Limits the query to Order Items that are past the process_at date.
     * This includes both processed and unprocessed items.
     *
     * @param $query
     * @return mixed
     */
    public function scopeDue($query)
    {
        return $query->where('process_at', '<=', now());
    }

    /**
     * Limits the query to Order Items that are ready to be processed.
     * This includes items that are both unprocessed and due.
     *
     * @param $query
     * @return mixed
     */
    public function scopeShouldProcess($query)
    {
        return $query->unprocessed()->due();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return OrderItemCollection
     */
    public function newCollection(array $models = [])
    {
        return new OrderItemCollection($models);
    }

    /**
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function toCollection()
    {
        return $this->newCollection([$this]);
    }

    /**
     * Called right before processing the item into an order.
     *
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function preprocess()
    {
        if ($this->orderableIsSet()) {
            return $this->orderable->preprocessOrderItem($this);
        }

        return $this->toCollection();
    }

    /**
     * @return bool
     */
    public function orderableIsSet()
    {
        return $this->orderable_id && $this->orderable_type;
    }

    /**
     * Called after processing the item into an order.
     *
     * @return $this
     */
    public function process()
    {
        if ($this->orderableIsSet()) {
            $result = $this->orderable->processOrderItem($this);
            $result->save();

            return $result;
        }

        return $this;
    }

    /**
     * Check whether the order item is processed into an order.
     *
     * @param  bool  $processed
     * @return bool
     */
    public function isProcessed($processed = true)
    {
        return empty($this->order_id) != $processed;
    }

    /**
     * Get the unit price before taxes and discounts.
     *
     * @return \Money\Money
     */
    public function getUnitPrice()
    {
        return $this->toMoney($this->unit_price);
    }

    /**
     * Get the order item total after taxes and discounts.
     *
     * @return \Money\Money
     */
    public function getTotal()
    {
        return $this->toMoney($this->total);
    }

    /**
     * Get the order item total before taxes and discounts.
     *
     * @return \Money\Money
     */
    public function getSubtotal()
    {
        return $this->toMoney($this->subtotal);
    }

    /**
     * The order item tax as a percentage.
     *
     * @return float
     *
     * @example 21.5
     */
    public function getTaxPercentage()
    {
        return (float) $this->tax_percentage;
    }

    /**
     * The discount as a money value.
     *
     * @return \Money\Money
     */
    public function getDiscount()
    {
        return $this->toMoney($this->discount);
    }

    /**
     * The order item tax as a money value.
     *
     * @return \Money\Money
     */
    public function getTax()
    {
        return $this->toMoney($this->tax);
    }

    /**
     * Handle a failed payment on the order item.
     * Invokes handlePaymentFailed on the orderable model.
     *
     * @return $this
     */
    public function handlePaymentFailed()
    {
        if ($this->orderableIsSet()) {
            $this->getOrderableClass()::handlePaymentFailed($this);
        }

        return $this;
    }

    /**
     * Handle a paid payment on the order item.
     * Invokes handlePaymentPaid on the orderable model.
     *
     * @return $this
     */
    public function handlePaymentPaid()
    {
        if ($this->orderableIsSet()) {
            $this->getOrderableClass()::handlePaymentPaid($this);
        }

        return $this;
    }

    /**
     * Handle a payment refund on the order item.
     * Invokes handlePaymentRefunded on the orderable model.
     *
     * @param  \Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return $this
     */
    public function handlePaymentRefunded(RefundItem $refundItem)
    {
        if ($this->orderableIsSet()) {
            $orderable = $this->getOrderableClass();
            if ($orderable instanceof IsRefundable) {
                $orderable::handlePaymentRefunded($refundItem);
            }
        }

        return $this;
    }

    /**
     * Handle a failed payment refund on the order item.
     * Invokes handlePaymentRefundFailed on the orderable model.
     *
     * @param  \Cashier\Mollie\Refunds\RefundItem  $refundItem
     * @return $this
     */
    public function handlePaymentRefundFailed(RefundItem $refundItem)
    {
        if ($this->orderableIsSet()) {
            $orderable = $this->getOrderableClass();
            if ($orderable instanceof IsRefundable) {
                $orderable::handlePaymentRefundFailed($refundItem);
            }
        }

        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the class of orderable_type.
     *
     * @return string
     */
    protected function getOrderableClass()
    {
        return Relation::getMorphedModel($this->orderable_type) ?? $this->orderable_type;
    }
}
