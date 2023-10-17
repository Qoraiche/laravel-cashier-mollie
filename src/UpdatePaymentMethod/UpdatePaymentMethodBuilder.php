<?php

namespace Cashier\Mollie\UpdatePaymentMethod;

use Illuminate\Database\Eloquent\Model;
use Cashier\Mollie\FirstPayment\Actions\AddBalance;
use Cashier\Mollie\FirstPayment\Actions\AddGenericOrderItem;
use Cashier\Mollie\FirstPayment\FirstPaymentBuilder;
use Cashier\Mollie\Http\RedirectToCheckoutResponse;
use Cashier\Mollie\Plan\Contracts\PlanRepository;
use Cashier\Mollie\Traits\HandlesMoneyRounding;
use Cashier\Mollie\UpdatePaymentMethod\Contracts\UpdatePaymentMethodBuilder as Contract;
use Money\Money;

class UpdatePaymentMethodBuilder implements Contract
{
    use HandlesMoneyRounding;

    /**
     * The billable model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * @var bool
     */
    protected $skipBalance = false;

    /**
     * UpdatePaymentMethodBuilder constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     */
    public function __construct(Model $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $payment = (new FirstPaymentBuilder($this->owner))
            ->setRedirectUrl(config('cashier_mollie.update_payment_method.redirect_url'))
            ->setFirstPaymentMethod($this->allowedPaymentMethods())
            ->setDescription(config('cashier_mollie.update_payment_method.description'))
            ->inOrderTo($this->getPaymentActions())
            ->create();

        $payment->update();

        return RedirectToCheckoutResponse::forPayment($payment);
    }

    /**
     * @return $this
     */
    public function skipBalance()
    {
        $this->skipBalance = true;

        return $this;
    }

    /**
     * @return array
     */
    protected function allowedPaymentMethods()
    {
        $paymentMethods = $this->owner->subscriptions->map(function ($subscription) {
            if ($subscription->active()) {
                $planModel = app(PlanRepository::class)::findOrFail($subscription->plan);

                return $planModel->firstPaymentMethod();
            }
        })->filter()->unique()->collapse();

        return $paymentMethods->all();
    }

    /**
     * @return \Cashier\Mollie\FirstPayment\Actions\AddBalance[]|\Cashier\Mollie\FirstPayment\Actions\AddGenericOrderItem[]
     */
    protected function getPaymentActions()
    {
        if ($this->skipBalance) {
            return [$this->addGenericItemAction()];
        }

        return [$this->addToBalanceAction()];
    }

    /**
     * @return \Cashier\Mollie\FirstPayment\Actions\AddBalance
     */
    protected function addToBalanceAction()
    {
        return
            new AddBalance(
                $this->owner,
                mollie_array_to_money(config('cashier_mollie.update_payment_method.amount')),
                1,
                config('cashier_mollie.update_payment_method.description')
            );
    }

    /**
     * @return \Cashier\Mollie\FirstPayment\Actions\AddGenericOrderItem
     */
    protected function addGenericItemAction()
    {
        $total = mollie_array_to_money(config('cashier_mollie.update_payment_method.amount'));
        $taxPercentage = $this->owner->taxPercentage() * 0.01;

        $subtotal = $this->subtotalForTotalIncludingTax($total, $taxPercentage);

        return new AddGenericOrderItem($this->owner, $subtotal, 1, config('cashier_mollie.update_payment_method.description'));
    }

    /**
     * @param  \Money\Money  $total
     * @param  float  $taxPercentage
     * @return \Money\Money
     */
    protected function subtotalForTotalIncludingTax(Money $total, float $taxPercentage)
    {
        $vat = $total->divide(
            sprintf('%.8F', 1 + $taxPercentage)
        )->multiply(
            sprintf('%.8F', $taxPercentage),
            $this->roundingMode($total, $taxPercentage)
        );

        return $total->subtract($vat);
    }
}
