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
    $requestedTotal = 0; // 👈 Para sumar la cantidad que quiere comprar ahora
    $itemsToSave = [];

    // 2. Recorrer los tipos de boletos enviados desde el formulario
    foreach ($request->input('tickets', []) as $ticketTypeId => $quantity) {
        $quantity = (int) $quantity;
        if ($quantity > 0) {
            $ticketType = TicketType::where('id', $ticketTypeId)
                ->where('event_id', $event->id)
                ->first();

            if ($ticketType) {
                $itemsToSave[] = [
                    'ticket_type_obj' => $ticketType,
                    'quantity' => $quantity,
                ];
                $totalAmount += $ticketType->price * $quantity;
                $requestedTotal += $quantity; // 👈 Acumulamos el total de boletos solicitados
            }
        }
    }

    // 3. Validar que realmente se haya seleccionado al menos un boleto con cantidad > 0
    if (empty($itemsToSave) || $totalAmount <= 0) {
        return back()->withErrors(['error' => 'Debes seleccionar al menos un boleto.']);
    }

    // 🛑 4. VALIDAR EL LÍMITE DE BOLETOS POR USUARIO ANTES DE CREAR NADA
    $maxLimit = (int) ($event->max_tickets_per_user ?? 0);

    if ($maxLimit > 0) {
        // Sumar todos los boletos que este usuario ya tiene (en órdenes o tickets activos/pagados/apartados)
        // Buscamos tanto en la tabla 'tickets' como en 'order_items' según tu estructura actual:
        $existingTicketsCount = (int) \App\Models\Ticket::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->sum('quantity');

        // Opcional: Sumar también los de órdenes pendientes vigentes por si acaso
        $pendingOrdersCount = (int) \App\Models\Order::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('items')
            ->get()
            ->sum(function($order) {
                return $order->items->sum('quantity');
            });

        $totalUserCurrent = $existingTicketsCount + $pendingOrdersCount;

        if (($totalUserCurrent + $requestedTotal) > $maxLimit) {
            $disponibles = max(0, $maxLimit - $totalUserCurrent);
            return back()->with('error', "Límite alcanzado: Ya cuentas con {$totalUserCurrent} boletos registrados para este evento.");
        }
    }

    // 5. 📝 CREAR PRIMERO LA ORDEN con estatus pendiente y su tiempo de expiración
    $expiryMinutes = $event->reservation_expiry_time ?? 5; // Por defecto 5 minutos

    $order = Order::create([
        'user_id' => Auth::id(),
        'event_id' => $event->id,
        'total_amount' => $totalAmount,
        'status' => 'pending',
        'expires_at' => now()->addMinutes($expiryMinutes), 
    ]);

    // 6. Guardar los ítems en la orden
    foreach ($itemsToSave as $itemData) {
        $ticketType = $itemData['ticket_type_obj'];
        $quantity = $itemData['quantity'];

        if (method_exists($order, 'items')) {
            $order->items()->create([
                'ticket_type_id' => $ticketType->id,
                'quantity' => $quantity,
                'price' => $ticketType->price,
            ]);
        }
    }

    // 7. Redirigir a la pantalla de selección de método de pago
    return redirect()->route('checkout.select', $order->id);
}

    public function selectPayment(Order $order)
    {
        // 🔴 SI EL TIEMPO YA EXPIRÓ:
        if ($order->status === 'pending' && now()->greaterThan($order->expires_at)) {
            
            \App\Models\Ticket::where('order_id', $order->id)
                ->update(['status' => 'cancelled']);

            $order->update(['status' => 'expired']);

            return redirect()->route('events.index')
                ->with('error', 'El tiempo de reserva ha expirado. La orden fue cancelada.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('events.index')
                ->with('error', 'Esta orden ya no es válida o ha expirado.');
        }

        return view('checkout.select', compact('order'));
    }

    public function myTickets()
{
    $user = Auth::user();

    $tickets = $user->tickets()
        // Forzamos que se excluya CUALQUIER ticket con estatus 'reserved'
        ->where('status', '!=', 'reserved')
        ->whereNotIn('status', ['cancelled', 'expired'])
        ->with(['event', 'ticketType', 'order'])
        ->latest()
        ->get();

    return view('user.tickets', compact('tickets'));
}
}