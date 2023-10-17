<?php

declare(strict_types=1);

namespace Cashier\Mollie\Plan\Contracts;

use Cashier\Mollie\Order\OrderItemPreprocessorCollection;
use Money\Money;

interface Plan
{
    /**
     * @return \Money\Money
     */
    public function amount();

    /**
     * @param  \Money\Money  $amount
     * @return Plan
     */
    public function setAmount(Money $amount);

    /**
     * @return string
     */
    public function description();

    /**
     * @return \Cashier\Mollie\Plan\Contracts\IntervalGeneratorContract
     */
    public function interval();

    /**
     * @return string
     */
    public function name();

    /**
     * The amount the customer is charged for a mandate payment.
     *
     * @return \Money\Money
     */
    public function firstPaymentAmount();

    /**
     * @param  \Money\Money  $firstPaymentAmount
     * @return Plan
     */
    public function setFirstPaymentAmount(Money $firstPaymentAmount);

    /**
     * @return array
     */
    public function firstPaymentMethod();

    /**
     * @param  array  $firstPaymentMethod
     * @return Plan
     */
    public function setFirstPaymentMethod($firstPaymentMethod);

    /**
     * The description for the mandate payment order item.
     *
     * @return string
     */
    public function firstPaymentDescription();

    /**
     * @param  string  $firstPaymentDescription
     * @return Plan
     */
    public function setFirstPaymentDescription(string $firstPaymentDescription);

    /**
     * @return string
     */
    public function firstPaymentRedirectUrl();

    /**
     * @param  string  $redirectUrl
     * @return Plan
     */
    public function setFirstPaymentRedirectUrl(string $redirectUrl);

    /**
     * @return string
     */
    public function firstPaymentWebhookUrl();

    /**
     * @param  string  $webhookUrl
     * @return Plan
     */
    public function setFirstPaymentWebhookUrl(string $webhookUrl);

    /**
     * @return \Cashier\Mollie\Order\OrderItemPreprocessorCollection
     */
    public function orderItemPreprocessors();

    /**
     * @param  \Cashier\Mollie\Order\OrderItemPreprocessorCollection  $preprocessors
     * @return \Cashier\Mollie\Plan\Contracts\Plan
     */
    public function setOrderItemPreprocessors(OrderItemPreprocessorCollection $preprocessors);
}
