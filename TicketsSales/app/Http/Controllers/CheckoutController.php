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

    /**
     * Muestra la pasarela de pago de Stripe Elements.
     */
/**
     * Muestra la pasarela de pago de Stripe Elements.
     */
    public function show(Order $order)
    {
        // Si la orden ya expiró, aplicamos el "Mocos", liberamos y mandamos al dashboard con aviso
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')
                ->with('error', 'El tiempo de tu reserva ha expirado y tus boletos han sido liberados.');
        }

        // ⏰ 1. VALIDAR SI LA RESERVA YA EXPIRÓ
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')->with('error', 'El tiempo de tu reserva ha expirado y los boletos han sido liberados.');
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
            'stripe_payment_intent_id' => $result['payment_intent_id']
        ]);

        return view('checkout.payment', [
            'order' => $order,
            'clientSecret' => $result['client_secret'],
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Muestra la vista donde el usuario elige Tarjeta o Contra Entrega.
     */
    public function showSelection(Order $order)
    {
        // Si la orden ya expiró, aplicamos el "Mocos", liberamos y mandamos al dashboard con aviso
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')
                ->with('error', 'El tiempo de tu reserva ha expirado y tus boletos han sido liberados.');
        }

        // ⏰ 2. VALIDAR SI LA RESERVA YA EXPIRÓ TAMBIÉN AQUÍ
        if ($order->checkExpiration()) {
            return redirect()->route('dashboard')->with('error', 'El tiempo de tu reserva ha expirado y los boletos han sido liberados.');
        }

        return view('checkout.select-method', compact('order'));
    }

    /**
     * Procesa la elección del método de pago.
     */
    public function processSelection(Request $request, Order $order)
{
    $request->validate([
        'payment_method' => 'required|in:stripe,cash_on_delivery',
    ]);

    if ($request->payment_method === 'stripe') {
        return redirect()->route('checkout.show', $order->id);
    } 

    $order->update([
        'status' => 'pending' 
    ]);

    Payment::create([
        'order_id' => $order->id,
        'gateway' => 'cash',
        'amount' => $order->total_amount,
        'currency' => 'MXN',
        'status' => 'pending',
    ]);

    // 🎟️ GENERAR LOS BOLETOS AQUÍ TAMBIÉN PARA CONTRA ENTREGA:
    if ($order->items && $order->items->count() > 0) {
        foreach ($order->items as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                \App\Models\Ticket::create([
                    'user_id' => $order->user_id,
                    'event_id' => $order->event_id,
                    'ticket_type_id' => $item->ticket_type_id,
                    'order_id' => $order->id,
                    'ticket_code' => (string) \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
                    'qr_code' => (string) \Illuminate\Support\Str::uuid(),
                ]);
            }
        }
    }

    return redirect()->route('dashboard')->with('success', '¡Orden apartada con éxito! Seleccionaste pago contra entrega.');
}

    /**
     * Valida el pago con tarjeta al regresar de Stripe (Evita el 404 y actualiza al instante).
     */
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
                $order->update(['status' => 'paid']);

                // 🎟️ GENERAR LOS BOLETOS CON ESTATUS 'paid':
                if ($order->items && $order->items->count() > 0) {
                    foreach ($order->items as $item) {
                        for ($i = 0; $i < $item->quantity; $i++) {
                            \App\Models\Ticket::create([
                                'user_id'        => $order->user_id,
                                'event_id'       => $order->event_id,
                                'ticket_type_id' => $item->ticket_type_id,
                                'ticket_code'    => (string) \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
                                'qr_code'        => (string) \Illuminate\Support\Str::uuid(),
                                'status'         => 'paid', // 👈 ¡CLAVE: debe ser 'paid'!
                            ]);
                        }
                    }
                }
            }
        }
    }

    return redirect()->route('dashboard')->with('success', '¡Pago realizado con éxito! Tus boletos ya han sido generados.');
}

    /**
     * Método auxiliar para asegurar que los boletos aparezcan en el dashboard del usuario.
     */
    private function generateTicketsForOrder(Order $order)
    {
        if (method_exists($order, 'items') && $order->items()->exists()) {
            foreach ($order->items as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    Ticket::firstOrCreate([
                        'order_id' => $order->id,
                        'ticket_type_id' => $item->ticket_type_id,
                    ], [
                        'user_id' => $order->user_id,
                        'event_id' => $order->event_id,
                        'qr_code' => (string) Str::uuid(),
                        'status' => 'valid',
                    ]);
                }
            }
        }
    }

    public function processPayment(Request $request, Order $order)
{
    // 1. Verificar si la orden ya expiró o no está pendiente
    if ($order->status !== 'pending' || now()->greaterThan($order->expires_at)) {
        
        // Borrar los tickets temporales por seguridad si no se borraron antes
        Ticket::where('order_id', $order->id)->where('status', 'reserved')->delete();
        
        $order->update(['status' => 'expired']);

        return redirect()->route('events.index')
            ->with('error', 'El tiempo para completar tu pago ha expirado. Tus boletos fueron liberados.');
    }

    // 2. Si sigue vigente, continuar con la creación de la sesión de Stripe...
}
}