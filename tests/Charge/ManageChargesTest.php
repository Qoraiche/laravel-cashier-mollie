<?php

namespace Cashier\Mollie\Tests\Charge;

use Cashier\Mollie\Charge\FirstPaymentChargeBuilder;
use Cashier\Mollie\Charge\MandatedChargeBuilder;
use Cashier\Mollie\Mollie\Contracts\GetMollieCustomer;
use Cashier\Mollie\Mollie\Contracts\GetMollieMandate;
use Cashier\Mollie\Tests\BaseTestCase;
use Cashier\Mollie\Tests\Fixtures\User;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Mandate;

class ManageChargesTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    /** @test */
    public function usingMandatedChargeBuilderWhenValidMandate()
    {
        $owner = User::factory()->create();

        $this->assertInstanceOf(FirstPaymentChargeBuilder::class, $owner->newCharge());
    }

    /** @test */
    public function useNewMandatedCharge()
    {
        $this->withMockedGetMollieCustomer();
        $this->withMockedGetMollieMandate();
        $owner = $this->getMandatedUser(true, [
            'mollie_mandate_id' => 'mdt_unique_mandate_id',
            'mollie_customer_id' => 'cst_unique_customer_id',
        ]);

        $this->assertInstanceOf(MandatedChargeBuilder::class, $owner->newCharge());
    }

    protected function withMockedGetMollieMandate(): void
    {
        $this->mock(GetMollieMandate::class, function ($mock) {
            $mandate = new Mandate(new MollieApiClient);
            $mandate->id = 'mdt_unique_mandate_id';
            $mandate->status = 'valid';
            $mandate->method = 'directdebit';

            return $mock->shouldReceive('execute')->with('cst_unique_customer_id', 'mdt_unique_mandate_id')->twice()->andReturn($mandate);
        });
    }

    protected function withMockedGetMollieCustomer(): void
    {
        $this->mock(GetMollieCustomer::class, function ($mock) {
            $customer = new Customer(new MollieApiClient);
            $customer->id = 'cst_unique_customer_id';

            return $mock->shouldReceive('execute')->with('cst_unique_customer_id')->twice()->andReturn($customer);
        });
    }
}
