<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\StripeService;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

   public function show(Order $order)
    {
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')
                ->with('error', 'El tiempo de tu reserva ha expirado y tus boletos han sido liberados.');
        }

        $result = $this->stripeService->createPaymentIntent($order->total_amount, 'mxn');

        if (!$result['success']) {
            return back()->with('error', 'Error al conectar con la pasarela de pagos: ' . $result['message']);
        }

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'gateway_transaction_id' => $result['payment_intent_id'],
            'amount' => $order->total_amount,
            'currency' => 'MXN',
            'status' => 'pending',
        ]);

        $order->update([
            'stripe_payment_intent_id' => $result['payment_intent_id'],
            'payment_method' => 'card'
        ]);

        // 🎟️ CREAMOS EL TICKET EN ESTATUS PENDIENTE DESDE EL INICIO (Protección si cierran la ventana)
       if ($order->items && $order->items->count() > 0 && $order->tickets()->count() === 0) {
            $totalQuantity = $order->items->sum('quantity');

            Ticket::create([
                'user_id'        => $order->user_id,
                'event_id'       => $order->event_id,
                'ticket_type_id' => $order->items->first()->ticket_type_id,
                'order_id'       => $order->id,
                'ticket_code'    => 'GF-' . strtoupper(Str::random(8)),
                'quantity'       => $totalQuantity,
                'status'         => 'paid_pending', // 👈 CLAVE: Aquí debe decir paid_pending
                'payment_method' => 'card',
            ]);
        }

        return view('checkout.payment', [
            'order' => $order,
            'clientSecret' => $result['client_secret'],
            'stripeKey' => config('services.stripe.key'),
        ]);
    }
    public function showSelection(Order $order)
    {
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')
                ->with('error', 'El tiempo de tu reserva ha expirado y tus boletos han sido liberados.');
        }

        return view('checkout.select-method', compact('order'));
    }

    public function processSelection(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,cash_on_delivery',
        ]);

        if ($request->payment_method === 'stripe') {
            return redirect()->route('checkout.show', $order->id);
        } 

        $order->update([
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery'
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'cash',
            'amount' => $order->total_amount,
            'currency' => 'MXN',
            'status' => 'pending',
        ]);

        // 🎟️ AQUÍ SÍ SE GENERA EL TICKET PORQUE ES UNA ORDEN COMPROMETIDA (CONTRA ENTREGA)
        if ($order->items && $order->items->count() > 0) {
            $totalQuantity = $order->items->sum('quantity');

            Ticket::create([
                'user_id'        => $order->user_id,
                'event_id'       => $order->event_id,
                'ticket_type_id' => $order->items->first()->ticket_type_id,
                'order_id'       => $order->id,
                'ticket_code'    => 'GF-' . strtoupper(Str::random(8)),
                'quantity'       => $totalQuantity,
                'status'         => 'paid_pending',
                'payment_method' => 'cash_on_delivery',
            ]);
        }

        return redirect()->route('dashboard')->with('success', '¡Orden apartada con éxito! Seleccionaste pago contra entrega.');
    }

   public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');

        if ($paymentIntentId) {
            Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $payment = Payment::where('gateway_transaction_id', $paymentIntentId)->first();

                if ($payment && $payment->status !== 'paid') {
                    $payment->update([
                        'status' => 'paid',
                        'payload' => $paymentIntent->toArray(),
                    ]);

                    $order = $payment->order;
                    $order->update([
                        'status' => 'paid',
                        'payment_method' => 'card'
                    ]);

                    // 🎟️ AQUÍ CAMBIAMOS EL ESTATUS A 'paid' (El boleto ya existía como paid_pending)
                    Ticket::where('order_id', $order->id)->update(['status' => 'paid']);
                }
            }
        }

        return redirect()->route('dashboard')->with('success', '¡Pago realizado con éxito! Tus boletos ya han sido validados.');
    }
}