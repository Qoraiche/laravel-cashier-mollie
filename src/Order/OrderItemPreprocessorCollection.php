<?php

namespace Cashier\Mollie\Order;

use Illuminate\Support\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Cashier\Mollie\Order\BaseOrderItemPreprocessor as Preprocessor;

/**
 * A collection of instantiable OrderItemPreprocessor class strings.
 */
class OrderItemPreprocessorCollection extends Collection
{
    /**
     * Initialize the preprocessors from a string array.
     *
     * @param  string[]  $value
     * @return \Cashier\Mollie\Order\OrderItemPreprocessorCollection
     */
    public static function fromArray($value)
    {
        $preprocessors = collect($value)->map(function ($class) {
            return app()->make($class);
        });

        return static::fromBaseCollection($preprocessors);
    }

    /**
     * @param  \Cashier\Mollie\Order\OrderItem  $item
     * @return \Cashier\Mollie\Order\OrderItemCollection
     */
    public function handle(OrderItem $item)
    {
        $items = $this->reduce(function ($carry, Preprocessor $preprocessor) {
            return $preprocessor->handle($carry);
        }, $item->toCollection());

        return new OrderItemCollection($items);
    }

    /**
     * Create an OrderItemCollection from a basic Collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Cashier\Mollie\Order\OrderItemPreprocessorCollection
     */
    public static function fromBaseCollection(BaseCollection $collection)
    {
        return new static($collection->all());
    }
}
