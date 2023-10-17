<?php

namespace Cashier\Mollie\Tests\Order;

use Cashier\Mollie\Order\Invoice;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\OrderFactory;
use Cashier\Mollie\Tests\Fixtures\User;

class OrderCollectionTest extends BaseTestCase
{
    /** @test */
    public function canGetInvoices()
    {
        $this->withPackageMigrations();
        $user = User::factory()->create();
        $orders = $user->orders()->saveMany(OrderFactory::new()->times(2)->make());

        $invoices = $orders->invoices();

        $this->assertCount(2, $invoices);
        $this->assertInstanceOf(Invoice::class, $invoices->first());
    }
}
