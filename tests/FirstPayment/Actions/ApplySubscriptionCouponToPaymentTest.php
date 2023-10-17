<?php

namespace Cashier\Mollie\Tests\FirstPayment\Actions;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Cashier\Mollie\FirstPayment\Actions\ApplySubscriptionCouponToPayment as Action;
use Cashier\Mollie\Order\OrderItemCollection;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Database\Factories\OrderItemFactory;
use Cashier\Mollie\Tests\Fixtures\User;

class ApplySubscriptionCouponToPaymentTest extends BaseTestCase
{
    private $action;

    private $coupon;

    private $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Cashier::useCurrency('eur');

        $this->withMockedCouponRepository();
        $this->coupon = app()->make(CouponRepository::class)->findOrFail('test-coupon');
        $this->owner = User::factory()->make();
        $orderItems = OrderItemFactory::new()->make([
            'unit_price' => 10000,
            'currency' => 'EUR',
        ])->toCollection();

        $this->action = new Action($this->owner, $this->coupon, $orderItems);
    }

    /** @test */
    public function testGetTotalReturnsDiscountSubtotal()
    {
        $this->assertMoneyEURCents(-500, $this->action->getTotal());
    }

    /** @test */
    public function testTaxDefaultsToZero()
    {
        $this->assertEquals(0, $this->action->getTaxPercentage());
        $this->assertMoneyEURCents(0, $this->action->getTax());
    }

    /** @test */
    public function testCreateFromPayloadReturnsNull()
    {
        $this->assertNull(Action::createFromPayload(['foo' => 'bar'], User::factory()->make()));
    }

    /** @test */
    public function testGetPayloadReturnsNull()
    {
        $this->assertNull($this->action->getPayload());
    }

    /** @test */
    public function testExecuteReturnsEmptyOrderItemCollection()
    {
        $result = $this->action->execute();
        $this->assertEquals(new OrderItemCollection, $result);
    }
}
