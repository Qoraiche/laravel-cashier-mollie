<?php

namespace Cashier\Mollie\Http\Controllers;

use Illuminate\Http\Request;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Mollie\Contracts\UpdateMolliePayment;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends BaseWebhookController
{
    /**
     * @param  Request  $request
     * @return Response
     *
     * @throws \Mollie\Api\Exceptions\ApiException Only in debug mode
     */
    public function handleWebhook(Request $request)
    {
        $payment = $this->getMolliePaymentById($request->get('id'));

        if ($payment) {
            $order = $this->getOrder($payment);

            if ($order && $order->mollie_payment_status !== $payment->status) {
                switch ($payment->status) {
                    case PaymentStatus::STATUS_PAID:
                        $order->handlePaymentPaid($payment);
                        $payment->webhookUrl = route('webhooks.mollie.aftercare');

                        /** @var UpdateMolliePayment $updateMolliePayment */
                        $updateMolliePayment = app()->make(UpdateMolliePayment::class);
                        $updateMolliePayment->execute($payment);

                        break;
                    case PaymentStatus::STATUS_FAILED:
                        $order->handlePaymentFailed($payment);

                        break;
                    default:
                        break;
                }
            }
        }

        return new Response(null, 200);
    }

    /**
     * @param  \Mollie\Api\Resources\Payment  $payment
     * @return \Cashier\Mollie\Order\Order|null
     */
    protected function getOrder(Payment $payment)
    {
        $order = Cashier::$orderModel::findByMolliePaymentId($payment->id);

        if (! $order && isset($payment->metadata, $payment->metadata->temporary_mollie_payment_id)) {
            $order = Cashier::$orderModel::findByMolliePaymentId($payment->metadata->temporary_mollie_payment_id);

            if ($order) {
                // Store the definite payment id.
                $order->update(['mollie_payment_id' => $payment->id]);
            }
        }

        return $order;
    }
}
