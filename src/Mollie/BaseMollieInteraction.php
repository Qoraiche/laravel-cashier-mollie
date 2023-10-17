<?php

declare(strict_types=1);

namespace Cashier\Mollie\Mollie;

use Mollie\Laravel\Wrappers\MollieApiWrapper as Mollie;

abstract class BaseMollieInteraction
{
    /**
     * @var \Mollie\Laravel\Facades\Mollie
     */
    protected $mollie;

    public function __construct(Mollie $mollie)
    {
        $this->mollie = $mollie;
    }
}
