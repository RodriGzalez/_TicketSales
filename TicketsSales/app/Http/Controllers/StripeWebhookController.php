<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                
                $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

                if ($order && $order->status !== 'paid') {
                    $order->update(['status' => 'paid', 'payment_method' => 'card']);
                    
                    Payment::where('order_id', $order->id)->update(['status' => 'paid']);

                    // 🎟️ AQUÍ CAMBIA EL TICKET A 'paid' AUTOMÁTICAMENTE
                    Ticket::where('order_id', $order->id)->update(['status' => 'paid']);
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                
                $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

                if ($order) {
                    $order->update(['status' => 'cancelled']);
                    Ticket::where('order_id', $order->id)->update(['status' => 'cancelled']);
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }
}

