<?php

use Illuminate\Support\Facades\Route;
use Cashier\Mollie\Cashier;
use Cashier\Mollie\Http\Controllers\AftercareWebhookController;
use Cashier\Mollie\Http\Controllers\FirstPaymentWebhookController;
use Cashier\Mollie\Http\Controllers\WebhookController;

Route::namespace('\Cashier\Mollie\Http\Controllers')->group(function () {
    Route::name('webhooks.mollie.default')->post(
        Cashier::webhookUrl(),
        [WebhookController::class, 'handleWebhook']
    );

    Route::name('webhooks.mollie.aftercare')->post(
        Cashier::aftercareWebhookUrl(),
        [AftercareWebhookController::class, 'handleWebhook']
    );

    Route::name('webhooks.mollie.first_payment')->post(
        Cashier::firstPaymentWebhookUrl(),
        [FirstPaymentWebhookController::class, 'handleWebhook']
    );
});
