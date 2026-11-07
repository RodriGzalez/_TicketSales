<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        // Inicializar Stripe con la llave secreta del archivo .env
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
    }

    /**
     * Crea un Payment Intent en Stripe para procesar el cargo de la orden.
     */
    public function createPaymentIntent(float $amount, string $currency = 'mxn')
    {
        try {
            // Stripe maneja los montos en centavos (ej. $100.00 MXN = 10000)
            $amountInCents = round($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => strtolower($currency),
                'automatic_payment_methods' => [
                    'enabled' => 'true',
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}