<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\TicketReservedMail;
use App\Traits\LogsAdminActivity;
use Illuminate\Support\Facades\Mail;


class TicketController extends Controller
{
    use LogsAdminActivity;
public function store(Request $request, Event $event)
{
    if ($event->status !== 'active') {
        return back()->with('error', 'Este evento no está disponible.');
    }

    $ticketInputs = $request->input('tickets', []);
    $requestedTotal = 0;

    // 1. Validar que al menos haya seleccionado algo y calcular el total pedido
    foreach ($ticketInputs as $typeId => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0) continue;

        $ticketType = \App\Models\TicketType::find($typeId);
        if (!$ticketType) continue;

        // Validar stock individual de este tipo
        $soldForType = (int) \App\Models\Ticket::where('ticket_type_id', $typeId)
            ->where('status', '!=', 'cancelled')
            ->sum('quantity');

        $remaining = $ticketType->stock - $soldForType;

        if ($qty > $remaining) {
            return back()->with('error', "Lo sentimos, el tipo de boleto '{$ticketType->name}' ya no cuenta con el stock suficiente.");
        }

        $requestedTotal += $qty;
    }

    if ($requestedTotal <= 0) {
        return back()->with('error', 'Debes seleccionar al menos un boleto.');
    }

   // 2. Validar límite de boletos por usuario (Blindado contra cualquier nombre de columna o estatus)
    $maxLimit = (int) ($event->max_tickets_per_user ?? $event->limite_boletos ?? 0); 

    if ($maxLimit > 0) {
        // Contamos TODOS los boletos que este usuario tenga para este evento (sin importar si dice paid, reserved, etc., excepto cancelados)
        $userTicketsCount = (int) \App\Models\Ticket::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->sum('quantity');

        if (($userTicketsCount + $requestedTotal) > $maxLimit) {
            $disponibles = max(0, $maxLimit - $userTicketsCount);
            return back()->with('error', "Límite superado: Ya cuentas con {$userTicketsCount} boletos para este evento. El límite máximo es de {$maxLimit} por usuario. Solo puedes adquirir {$disponibles} más.");
        }
    }

    // 3. Validar capacidad total del evento
    $soldTotal = (int) $event->tickets()->where('status', '!=', 'cancelled')->sum('quantity');
    if (($soldTotal + $requestedTotal) > (int) $event->total_capacity) {
        return back()->with('error', 'Lo sentimos, no hay suficientes boletos disponibles en total.');
    }

    // 4. CREAR EL REGISTRO Y ENVIAR EL CORREO (Una sola pasada limpia)
    foreach ($ticketInputs as $typeId => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0) continue;

        $ticket = \App\Models\Ticket::create([
            'user_id'        => auth()->id(),
            'event_id'       => $event->id,
            'ticket_type_id' => $typeId,
            'quantity'       => $qty,
            'ticket_code'    => 'GF-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'status'         => 'reserved',
            // 🎯 AQUÍ ESTÁ LA SOLUCIÓN DEFINITIVA CONECTADA A BD:
            'payment_method' => $request->input('payment_method', 'cash_on_delivery'), 
            'expires_at'     => \Carbon\Carbon::now()->addMinutes($event->reservation_expiry_time),
        ]);

        // Enviar correo con QR por cada tipo de boleto seleccionado
        \Illuminate\Support\Facades\Mail::to($request->user()->email)
            ->send(new \App\Mail\TicketReservedMail($ticket));
    }

    return redirect()->route('dashboard')->with('success', '¡Orden de boletos apartada con éxito!');
}

public function destroy($id)
{
    $ticket = \App\Models\Ticket::with('order', 'ticketType')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->whereIn('status', ['reserved', 'paid_pending'])
        ->firstOrFail();

    // 1. Actualizamos el estatus del ticket a cancelado
    $ticket->update(['status' => 'cancelled']);

    // 2. 🔑 CLAVE: Si el ticket tiene una orden asociada, cancelarla también para liberar la reserva
    if ($ticket->order) {
        $ticket->order->update(['status' => 'cancelled']);
    }

    // 3. Opcional: Devolvemos el stock al tipo de boleto (si manejas inventario numérico)
    if ($ticket->ticketType) {
        $ticket->ticketType->increment('stock', $ticket->quantity);
    }

    // 4. Registramos el log en la bitácora
    self::logActivity(
        'UPDATED',
        'Boletos',
        "El usuario canceló el boleto con folio: {$ticket->ticket_code}"
    );

    return back()->with('success', 'El boleto ha sido cancelado exitosamente.');
}


}