<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket; 

class OrderController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // 1. Validar que se reciba el arreglo de tickets
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'integer|min:0',
        ]);

        $totalAmount = 0;
        $itemsToSave = [];

        // 2. Recorrer los tipos de boletos enviados desde el formulario
        foreach ($request->input('tickets', []) as $ticketTypeId => $quantity) {
            if ($quantity > 0) {
                $ticketType = TicketType::where('id', $ticketTypeId)
                    ->where('event_id', $event->id)
                    ->first();

                if ($ticketType) {
                    $itemsToSave[] = [
                        'ticket_type_obj' => $ticketType,
                        'quantity' => (int) $quantity,
                    ];
                    $totalAmount += $ticketType->price * $quantity;
                }
            }
        }

        // 3. Validar que realmente se haya seleccionado al menos un boleto con cantidad > 0
        if (empty($itemsToSave) || $totalAmount <= 0) {
            return back()->withErrors(['error' => 'Debes seleccionar al menos un boleto.']);
        }

        // 4. 📝 CREAR PRIMERO LA ORDEN con estatus pendiente y su tiempo de expiración
        $expiryMinutes = $event->reservation_expiry_time ?? 5; // Por defecto 5 minutos

        $order = Order::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes($expiryMinutes), 
        ]);

        // 5. Determinar el estatus del boleto según el método de pago seleccionado
        // (Si se define en el checkout posterior o viene del request, aquí asignamos por defecto 'reserved')
        $ticketStatus = 'reserved'; 

        foreach ($itemsToSave as $itemData) {
            $ticketType = $itemData['ticket_type_obj'];
            $quantity = $itemData['quantity'];

            for ($i = 0; $i < $quantity; $i++) {
                Ticket::create([
                    'event_id' => $event->id,
                    'ticket_type_id' => $ticketType->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'status' => 'reserved', // 👈 Nace como reservado temporalmente para el temporizador
                    'ticket_code' => 'TICK-' . strtoupper(uniqid()),
                ]);
            }

            // Opcional: si guardas los detalles en una tabla de items de orden
            if (method_exists($order, 'items')) {
                $order->items()->create([
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => $quantity,
                    'price' => $ticketType->price,
                ]);
            }
        }

        // 7. Redirigir a la pantalla de selección de método de pago de esta orden
        return redirect()->route('checkout.select', $order->id);
    }

    public function selectPayment(Order $order)
{
    // 🔴 SI EL TIEMPO YA EXPIRÓ:
    if ($order->status === 'pending' && now()->greaterThan($order->expires_at)) {
        
        // Opción A: Marcarlos como 'cancelled' para que sigan existiendo en BD pero liberando stock
        \App\Models\Ticket::where('order_id', $order->id)
            ->update(['status' => 'cancelled']);

        // Opción B (Si prefieres borrarlos físicamente para que no estorben):
        // \App\Models\Ticket::where('order_id', $order->id)->delete();

        // Marcar la orden como expirada/cancelada
        $order->update(['status' => 'expired']);

        return redirect()->route('events.index')
            ->with('error', 'El tiempo de reserva ha expirado. Los boletos fueron cancelados y el stock liberado.');
    }

    if ($order->status !== 'pending') {
        return redirect()->route('events.index')
            ->with('error', 'Esta orden ya no es válida o ha expirado.');
    }

    return view('checkout.select', compact('order'));
}

public function myTickets()
{
    $tickets = Auth::user()->tickets()
        ->whereNotIn('status', ['reserved', 'cancelled', 'expired'])
        ->where(function($query) {
            $query->where('ticket_code', 'NOT LIKE', 'TICK-%')
                  ->orWhereIn('status', ['paid', 'paid_pending', 'liquidate']);
        })
        ->with(['event', 'ticketType'])
        ->latest()
        ->get();

    return view('user.tickets', compact('tickets'));
}

}