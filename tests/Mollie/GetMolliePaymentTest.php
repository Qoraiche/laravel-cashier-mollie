<?php

declare(strict_types=1);

namespace Cashier\Mollie\Tests\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMolliePayment;
use Mollie\Api\Resources\Payment;

class GetMolliePaymentTest extends BaseMollieInteractionTest
{
    /**
     * @test
     * @group mollie_integration
     */
    public function testExecute()
    {
        /** @var GetMolliePayment $action */
        $action = $this->app->make(GetMolliePayment::class);
        $id = $this->getMandatePaymentID();
        $result = $action->execute($id);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($id, $result->id);
    }
}
