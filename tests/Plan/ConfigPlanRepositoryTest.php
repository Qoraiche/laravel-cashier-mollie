<?php

namespace Cashier\Mollie\Tests\Plan;

use Illuminate\Support\Facades\Config;
use Cashier\Mollie\Coupon\CouponOrderItemPreprocessor;
use Cashier\Mollie\Exceptions\PlanNotFoundException;
use Cashier\Mollie\Order\OrderItemPreprocessorCollection;
use Cashier\Mollie\Order\PersistOrderItemsPreprocessor;
use Cashier\Mollie\Plan\ConfigPlanRepository;
use Cashier\Mollie\Plan\Contracts\IntervalGeneratorContract;
use Cashier\Mollie\Plan\Contracts\Plan;
use Cashier\Mollie\Tests\BaseTestCase;

class ConfigPlanRepositoryTest extends BaseTestCase
{
    protected $firstPaymentDefaultsArray = [
        'redirect_url' => 'https://www.foo-redirect-bar.com',
        'webhook_url' => 'https://www.foo-webhook-bar.com',
        'method' => ['ideal'],
        'amount' => [
            'value' => '0.05',
            'currency' => 'EUR',
        ],
        'description' => 'Test first payment',
    ];

    protected $testPlanArray = [
        'amount' => [
            'value' => '10.00',
            'currency' => 'EUR',
        ],
        'interval' => '1 month',
        'description' => 'Test subscription (monthly)',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Set config for this runtime.
        Config::set('cashier_plans.plans.Test', $this->testPlanArray);
    }

    /** @test */
    public function findReturnsNullWhenNotFound()
    {
        $this->assertNull(ConfigPlanRepository::find('some_wrong_name'));
    }

    /** @test */
    public function findReturnsPlanWhenFound()
    {
        $this->assertInstanceOf(Plan::class, ConfigPlanRepository::find('Test'));
    }

    /** @test
     * @throws \Cashier\Mollie\Exceptions\PlanNotFoundException
     */
    public function findOrFailCorrect()
    {
        $this->assertInstanceOf(Plan::class, ConfigPlanRepository::findOrFail('Test'));
    }

    /** @test */
    public function findOrFailWrong()
    {
        $this->expectException(PlanNotFoundException::class);
        ConfigPlanRepository::findOrFail('some_wrong_name');
    }

    /** @test */
    public function findIsCaseSensitive()
    {
        $this->assertNull(ConfigPlanRepository::find('test'));
        $this->assertInstanceOf(Plan::class, ConfigPlanRepository::find('Test'));
    }

    /** @test */
    public function populatesPlanProperlyWithoutDefaultsSet()
    {
        Config::set('cashier_plans.defaults', []); // clear Plan defaults
        $plan = ConfigPlanRepository::findOrFail('Test');

        $this->assertNull($plan->firstPaymentDescription());
        $this->assertNull($plan->firstPaymentAmount());
        $this->assertNull($plan->firstPaymentMethod());
        $this->assertNull($plan->firstPaymentRedirectUrl());
        $this->assertNull($plan->firstPaymentWebhookUrl());

        $this->assertMoneyEURCents(1000, $plan->amount());
        $this->assertEquals('Test subscription (monthly)', $plan->description());
        $this->assertEquals('Test', $plan->name());
        $this->assertInstanceOf(IntervalGeneratorContract::class, $plan->interval());
        $this->assertCarbon(now()->addMonth(), $plan->interval()->getEndOfNextSubscriptionCycle());
        $this->assertInstanceOf(OrderItemPreprocessorCollection::class, $plan->orderItemPreprocessors());
        $this->assertCount(0, $plan->orderItemPreprocessors());
    }

    /** @test */
    public function populatesPlanProperlyWithDefaultsSet()
    {
        Config::set('cashier_plans.defaults.first_payment', $this->firstPaymentDefaultsArray);
        $plan = ConfigPlanRepository::findOrFail('Test');

        $this->assertEquals('Test first payment', $plan->firstPaymentDescription());
        $this->assertMoneyEURCents(5, $plan->firstPaymentAmount());
        $this->assertEquals(['ideal'], $plan->firstPaymentMethod());
        $this->assertEquals('https://www.foo-redirect-bar.com', $plan->firstPaymentRedirectUrl());
        $this->assertEquals('https://www.foo-webhook-bar.com', $plan->firstPaymentWebhookUrl());

        $this->assertMoneyEURCents(1000, $plan->amount());
        $this->assertEquals('Test subscription (monthly)', $plan->description());
        $this->assertEquals('Test', $plan->name());
        $this->assertInstanceOf(IntervalGeneratorContract::class, $plan->interval());
        $this->assertCarbon(now()->addMonth(), $plan->interval()->getEndOfNextSubscriptionCycle());
        $this->assertInstanceOf(OrderItemPreprocessorCollection::class, $plan->orderItemPreprocessors());
        $this->assertCount(2, $plan->orderItemPreprocessors());
        $this->assertEquals([
            new CouponOrderItemPreprocessor,
            new PersistOrderItemsPreprocessor,
        ], $plan->orderItemPreprocessors()->all());
    }
}
