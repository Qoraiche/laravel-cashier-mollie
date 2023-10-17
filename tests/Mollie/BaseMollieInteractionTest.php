<?php

declare(strict_types=1);

namespace Laravel\Cashier\Mollie\Tests\Mollie;

use Laravel\Cashier\Mollie\Tests\BaseTestCase;

abstract class BaseMollieInteractionTest extends BaseTestCase
{
    protected $interactWithMollieAPI = true;
}
