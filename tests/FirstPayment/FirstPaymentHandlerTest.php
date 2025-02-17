<?php

namespace Cashier\Mollie\Tests\FirstPayment;

use Illuminate\Support\Facades\Event;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\MandateUpdated;
use Cashier\Mollie\FirstPayment\Actions\AddBalance;
use Cashier\Mollie\FirstPayment\FirstPaymentHandler;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Fixtures\User;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment as MolliePayment;
use Mollie\Api\Types\PaymentStatus;

class FirstPaymentHandlerTest extends BaseTestCase
{
    /** @test */
    public function handlesMolliePayments()
    {
        $this->withPackageMigrations();
        Event::fake();

        $molliePayment = $this->getMandatePaymentStub();

        $owner = User::factory()->create([
            'id' => $molliePayment->metadata->owner->id,
            'mollie_customer_id' => 'cst_unique_customer_id',
        ]);
        Cashier::$paymentModel::createFromMolliePayment($molliePayment, $owner);

        $handler = new FirstPaymentHandler($molliePayment);

        $this->assertTrue($owner->is($handler->getOwner()));

        $actions = $handler->getActions();
        $this->assertCount(2, $actions);

        $firstAction = $actions[0];
        $this->assertInstanceOf(AddBalance::class, $firstAction);
        $this->assertMoneyEURCents(500, $firstAction->getTotal());
        $this->assertEquals('Test add balance 1', $firstAction->getDescription());

        $secondAction = $actions[1];
        $this->assertInstanceOf(AddBalance::class, $secondAction);
        $this->assertMoneyEURCents(500, $secondAction->getTotal());
        $this->assertEquals('Test add balance 2', $secondAction->getDescription());

        $this->assertFalse($owner->hasCredit());
        $this->assertNull($owner->mollie_mandate_id);

        $this->assertEquals(0, $owner->orderItems()->count());
        $this->assertEquals(0, $owner->orders()->count());

        $order = $handler->execute();

        $owner = $owner->fresh();

        $this->assertTrue($owner->hasCredit());
        $credit = $owner->credit('EUR');
        $this->assertMoneyEURCents(1000, $credit->money());

        $this->assertEquals(2, $owner->orderItems()->count());
        $this->assertEquals(1, $owner->orders()->count());

        $this->assertInstanceOf(Cashier::$orderModel, $order);
        $this->assertTrue($order->isProcessed());

        $this->assertEquals(1, $order->payments()->count());
        $localPayment = $order->payments()->first();
        $this->assertInstanceOf(Cashier::$paymentModel, $localPayment);
        $this->assertEquals('paid', $localPayment->mollie_payment_status);
        $this->assertMoneyEURCents(1000, $localPayment->getAmount());
        $this->assertCount(2, $localPayment->first_payment_actions);
        $this->assertEquals(2, $order->items()->count());

        $this->assertNotNull($owner->mollie_mandate_id);
        $this->assertEquals($molliePayment->mandateId, $owner->mollie_mandate_id);

        Event::assertDispatched(MandateUpdated::class, function (MandateUpdated $e) use ($owner, $molliePayment) {
            $this->assertTrue($e->owner->is($owner));
            $this->assertSame($e->payment->id, $molliePayment->id);

            return true;
        });
    }

    protected function getMandatePaymentStub(): MolliePayment
    {
        $payment = new MolliePayment(new MollieApiClient());
        $payment->sequenceType = 'first';
        $payment->id = 'tr_unique_mandate_payment_id';
        $payment->customerId = 'cst_unique_customer_id';
        $payment->mandateId = 'mdt_unique_mandate_id';
        $payment->amount = (object) ['value' => '10.00', 'currency' => 'EUR'];
        $payment->status = PaymentStatus::STATUS_PAID;
        $payment->metadata = json_decode(json_encode([
            'owner' => [
                'type' => User::class,
                'id' => 1,
            ],
            'actions' => [
                [
                    'handler' => AddBalance::class,
                    'description' => 'Test add balance 1',
                    'subtotal' => [
                        'currency' => 'EUR',
                        'value' => '5.00',
                    ],
                    'taxPercentage' => 0,
                ],
                [
                    'handler' => AddBalance::class,
                    'description' => 'Test add balance 2',
                    'subtotal' => [
                        'currency' => 'EUR',
                        'value' => '5.00',
                    ],
                    'taxPercentage' => 0,
                ],
            ],
        ]));

        return $payment;
    }
}
