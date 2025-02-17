<?php

namespace Cashier\Mollie;

use Illuminate\Support\ServiceProvider;
use Cashier\Mollie\Console\Commands\CashierInstall;
use Cashier\Mollie\Console\Commands\CashierRun;
use Cashier\Mollie\Console\Commands\CashierUpdate;
use Cashier\Mollie\Coupon\ConfigCouponRepository;
use Cashier\Mollie\Coupon\Contracts\CouponRepository;
use Cashier\Mollie\Mollie\RegistersMollieInteractions;
use Cashier\Mollie\Order\Contracts\MaximumPayment as MaximumPaymentContract;
use Cashier\Mollie\Order\Contracts\MinimumPayment as MinimumPaymentContract;
use Cashier\Mollie\Plan\ConfigPlanRepository;
use Cashier\Mollie\Plan\Contracts\PlanRepository;
use Mollie\Laravel\MollieServiceProvider;

class CashierServiceProvider extends ServiceProvider
{
    use RegistersMollieInteractions;

    const PACKAGE_VERSION = '2.10.0';

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->mergeConfig();

        if (Cashier::$registersRoutes) {
            $this->loadRoutesFrom(__DIR__.'/../routes/webhooks.php');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cashier');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'cashier');

        mollie()->addVersionString('MollieLaravelCashier/'.self::PACKAGE_VERSION);

        if ($this->app->runningInConsole()) {
            $this->publishMigrations('cashier-migrations');
            $this->publishConfig('cashier-configs');
            $this->publishViews('cashier-views');
            $this->publishTranslations('cashier-translations');
            $this->publishUpdate('cashier-update');
        }

        $this->configureCurrency();
        $this->configureCurrencyLocale();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(MollieServiceProvider::class);
        $this->registerMollieInteractions($this->app);
        $this->app->bind(PlanRepository::class, ConfigPlanRepository::class);
        $this->app->singleton(CouponRepository::class, function () {
            return new ConfigCouponRepository(
                config('cashier_coupons.defaults'),
                config('cashier_coupons.coupons')
            );
        });
        $this->app->bind(MinimumPaymentContract::class, MinimumPayment::class);
        $this->app->bind(MaximumPaymentContract::class, MaximumPayment::class);

        $this->commands([
            CashierInstall::class,
            CashierRun::class,
            CashierUpdate::class,
        ]);

        $this->app->register(EventServiceProvider::class);
    }

    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cashier_mollie.php', 'cashier_mollie');
        $this->mergeConfigFrom(__DIR__.'/../config/cashier_coupons.php', 'cashier_coupons');
        $this->mergeConfigFrom(__DIR__.'/../config/cashier_plans.php', 'cashier_plans');
    }

    protected function publishMigrations(string $tag)
    {
        if (Cashier::$runsMigrations) {
            $prefix = 'migrations/'.date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_applied_coupons_table.php.stub' => database_path($prefix.'_create_applied_coupons_table.php'),
                __DIR__.'/../database/migrations/create_redeemed_coupons_table.php.stub' => database_path($prefix.'_create_redeemed_coupons_table.php'),
                __DIR__.'/../database/migrations/create_credits_table.php.stub' => database_path($prefix.'_create_credits_table.php'),
                __DIR__.'/../database/migrations/create_orders_table.php.stub' => database_path($prefix.'_create_orders_table.php'),
                __DIR__.'/../database/migrations/create_order_items_table.php.stub' => database_path($prefix.'_create_order_items_table.php'),
//                __DIR__.'/../database/migrations/create_subscriptions_table.php.stub' => database_path($prefix.'_create_subscriptions_table.php'),
                __DIR__.'/../database/migrations/create_payments_table.php.stub' => database_path($prefix.'_create_payments_table.php'),
                __DIR__.'/../database/migrations/create_refund_items_table.php.stub' => database_path($prefix.'_create_refund_items_table.php'),
                __DIR__.'/../database/migrations/create_refunds_table.php.stub' => database_path($prefix.'_create_refunds_table.php'),
            ], $tag);
        }
    }

    protected function publishUpdate(string $tag)
    {
        if (Cashier::$runsMigrations) {
            $prefix = 'migrations/'.date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/upgrade_to_cashier_v2.php.stub' => database_path($prefix.'_upgrade_to_cashier_v2.php'),
            ], $tag);
        }
    }

    protected function publishConfig(string $tag)
    {
        $this->publishes([
            __DIR__.'/../config/cashier_mollie.php' => config_path('cashier_mollie.php'),
            __DIR__.'/../config/cashier_coupons.php' => config_path('cashier_coupons.php'),
            __DIR__.'/../config/cashier_plans.php' => config_path('cashier_plans.php'),
        ], $tag);
    }

    protected function publishTranslations(string $tag)
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/cashier'),
        ], $tag);
    }

    protected function publishViews(string $tag)
    {
        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->basePath('resources/views/vendor/cashier'),
        ], $tag);
    }

    protected function configureCurrency()
    {
        $currency = config('cashier_mollie.currency', false);
        if ($currency) {
            Cashier::useCurrency($currency);
        }
    }

    protected function configureCurrencyLocale()
    {
        $locale = config('cashier_mollie.currency_locale', false);
        if ($locale) {
            Cashier::useCurrencyLocale($locale);
        }
    }
}
