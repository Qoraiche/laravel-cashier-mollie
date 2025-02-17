<?php

namespace Cashier\Mollie\Tests;

use Cashier\Mollie\Cashier;
use Cashier\Mollie\CashierServiceProvider;

class CashierServiceProviderTest extends BaseTestCase
{
    /** @test */
    public function canOptionallySetCurrencyInConfig()
    {
        $this->assertEquals('INEXISTENT', config('cashier_mollie.currency', 'INEXISTENT'));

        $this->assertEquals('€', Cashier::usesCurrencySymbol());
        $this->assertEquals('eur', Cashier::usesCurrency());

        config(['cashier.currency' => 'usd']);
        $this->rebootCashierServiceProvider();

        $this->assertEquals('usd', Cashier::usesCurrency());
        $this->assertEquals('$', Cashier::usesCurrencySymbol());
    }

    /** @test */
    public function canOptionallySetCurrencyLocaleInConfig()
    {
        $this->assertEquals('INEXISTENT', config('cashier_mollie.currency_locale', 'INEXISTENT'));
        $this->assertEquals('de_DE', Cashier::usesCurrencyLocale());

        config(['cashier.currency_locale' => 'nl_NL']);
        $this->rebootCashierServiceProvider();

        $this->assertEquals('nl_NL', Cashier::usesCurrencyLocale());
    }

    protected function rebootCashierServiceProvider()
    {
        tap(new CashierServiceProvider($this->app))->register()->boot();
    }
}
