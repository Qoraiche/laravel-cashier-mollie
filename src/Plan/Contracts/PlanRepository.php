<?php

declare(strict_types=1);

namespace Cashier\Mollie\Plan\Contracts;

interface PlanRepository
{
    /**
     * @param  string  $name
     * @return null|\Cashier\Mollie\Plan\Contracts\Plan
     */
    public static function find(string $name);

    /**
     * @param  string  $name
     * @return \Cashier\Mollie\Plan\Contracts\Plan
     */
    public static function findOrFail(string $name);
}
