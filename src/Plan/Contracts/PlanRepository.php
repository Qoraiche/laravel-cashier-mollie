<?php

declare(strict_types=1);

namespace Laravel\Cashier\Mollie\Plan\Contracts;

interface PlanRepository
{
    /**
     * @param  string  $name
     * @return null|\Laravel\Cashier\Mollie\Plan\Contracts\Plan
     */
    public static function find(string $name);

    /**
     * @param  string  $name
     * @return \Laravel\Cashier\Mollie\Plan\Contracts\Plan
     */
    public static function findOrFail(string $name);
}
