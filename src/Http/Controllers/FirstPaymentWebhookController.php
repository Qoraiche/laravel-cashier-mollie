<?php

namespace Cashier\Mollie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Cashier\Mollie\Events\FirstPaymentFailed;
use Cashier\Mollie\Events\FirstPaymentPaid;
use Cashier\Mollie\FirstPayment\FirstPaymentHandler;
use Cashier\Mollie\Mollie\Contracts\UpdateMolliePayment;
use Symfony\Component\HttpFoundation\Response;

class FirstPaymentWebhookController extends BaseWebhookController
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
            if ($payment->isPaid()) {
                $order = (new FirstPaymentHandler($payment))->execute();
                $payment->webhookUrl = route('webhooks.mollie.aftercare');

                /** @var UpdateMolliePayment $updateMolliePayment */
                $updateMolliePayment = app()->make(UpdateMolliePayment::class);
                $payment = $updateMolliePayment->execute($payment);

                Event::dispatch(new FirstPaymentPaid($payment, $order));
            } elseif ($payment->isFailed()) {
                Event::dispatch(new FirstPaymentFailed($payment));
            }
        }

        return new Response(null, 200);
    }
}
