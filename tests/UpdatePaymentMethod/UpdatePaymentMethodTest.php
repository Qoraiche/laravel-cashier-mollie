<?php

namespace Cashier\Mollie\Tests\UpdatePaymentMethod;

use Illuminate\Support\Facades\Event;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Events\MandateUpdated;
use Cashier\Mollie\FirstPayment\Actions\AddBalance;
use Cashier\Mollie\FirstPayment\FirstPaymentHandler;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Fixtures\User;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

class UpdatePaymentMethodTest extends BaseTestCase
{
    /** @test */
    public function canUpdatePaymentMethod()
    {
        $this->withPackageMigrations();

        $owner = User::factory()->create([
            'mollie_mandate_id' => 'mdt_unique_mandate_id',
        ]);

        $newPayment = $this->getNewMandatePaymentStub();
        Cashier::$paymentModel::createFromMolliePayment($newPayment, $owner);

        $newHandler = new FirstPaymentHandler($newPayment);

        $this->assertTrue($owner->is($newHandler->getOwner()));

        $actions = $newHandler->getActions();
        $this->assertCount(1, $actions);

        $updatePaymentAction = $actions[0];
        $this->assertInstanceOf(AddBalance::class, $updatePaymentAction);
        $this->assertMoneyEURCents(100, $updatePaymentAction->getTotal());
        $this->assertEquals('Payment method updated', $updatePaymentAction->getDescription());

        $this->assertNotNull($owner->mollie_mandate_id);

        Event::fake();
        $order = $newHandler->execute();

        $owner = $owner->fresh();

        $this->assertTrue($owner->hasCredit());
        $credit = $owner->credit('EUR');
        $this->assertMoneyEURCents(100, $credit->money());

        $this->assertEquals(1, $owner->orderItems()->count());
        $this->assertEquals(1, $owner->orders()->count());

        $this->assertInstanceOf(Cashier::$orderModel, $order);
        $this->assertTrue($order->isProcessed());

        $this->assertEquals(1, $order->items()->count());
        $this->assertEquals($newPayment->mandateId, $owner->mollie_mandate_id);
        $owner = $owner->fresh();

        Event::assertDispatched(MandateUpdated::class, function (MandateUpdated $e) use ($owner, $newPayment) {
            $this->assertTrue($e->owner->is($owner));
            $this->assertSame($e->payment->id, $newPayment->id);

            return true;
        });
    }

    protected function getNewMandatePaymentStub(): Payment
    {
        $newPayment = new Payment(new MollieApiClient());
        $newPayment->sequenceType = 'first';
        $newPayment->id = 'new_tr_unique_mandate_payment_id';
        $newPayment->customerId = 'cst_unique_customer_id';
        $newPayment->mandateId = 'new_mdt_unique_mandate_id';
        $newPayment->amount = (object) ['value' => '1.00', 'currency' => 'EUR'];
        $newPayment->metadata = json_decode(json_encode([
            'owner' => [
                'type' => User::class,
                'id' => 1,
            ],
            'actions' => [
                [
                    'handler' => AddBalance::class,
                    'description' => 'Payment method updated',
                    'subtotal' => [
                        'currency' => 'EUR',
                        'value' => '1.00',
                    ],
                    'taxPercentage' => 0,
                    'quantity' => 1,
                ],
            ],
        ]));

        return $newPayment;
    }
}
