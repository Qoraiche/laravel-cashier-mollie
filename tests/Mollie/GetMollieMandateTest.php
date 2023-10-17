<?php

declare(strict_types=1);

namespace Cashier\Mollie\Tests\Mollie;

use Cashier\Mollie\Mollie\Contracts\GetMollieMandate;
use Mollie\Api\Resources\Mandate;

class GetMollieMandateTest extends BaseMollieInteractionTest
{
    /**
     * @test
     * @group mollie_integration
     */
    public function testExecute()
    {
        /** @var \Cashier\Mollie\Mollie\GetMollieMandate $action */
        $action = $this->app->make(GetMollieMandate::class);
        $customerId = $this->getMandatedCustomerId();
        $mandateId = $this->getMandateId();
        $result = $action->execute($customerId, $mandateId);

        $this->assertInstanceOf(Mandate::class, $result);
        $this->assertEquals($mandateId, $result->id);
    }
}
