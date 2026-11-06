<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\TicketReservedMail;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
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

    // 2. Validar límite de boletos por usuario
    $userTicketsCount = (int) $event->tickets()
        ->where('user_id', auth()->id())
        ->where('status', '!=', 'cancelled')
        ->sum('quantity');

    if (($userTicketsCount + $requestedTotal) > $event->max_tickets_per_user) {
        $disponiblesParaUsuario = $event->max_tickets_per_user - $userTicketsCount;

        if ($disponiblesParaUsuario <= 0) {
            return back()->with('error', 'Límite alcanzado: Ya tienes ' . $userTicketsCount . ' boletos activos para este evento.');
        }

        return back()->with('error', 'Solo puedes apartar ' . $disponiblesParaUsuario . ' boleto(s) más para respetar el límite de este evento.');
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
            'expires_at'     => \Carbon\Carbon::now()->addMinutes($event->reservation_expiry_time),
        ]);

        // Enviar correo con QR por cada tipo de boleto seleccionado
        \Illuminate\Support\Facades\Mail::to($request->user()->email)
            ->send(new \App\Mail\TicketReservedMail($ticket));
    }

    return redirect()->route('dashboard')->with('success', '¡Orden de boletos apartada con éxito!');
}

public function destroy(\App\Models\Ticket $ticket)
{
    // Seguridad: verificar que el ticket pertenezca al usuario logueado
    if ($ticket->user_id !== auth()->id()) {
        abort(403);
    }

    // Cambiar estatus a cancelado para liberar el cupo y el stock
    $ticket->update(['status' => 'cancelled']);

    return redirect()->route('dashboard')->with('success', '¡Boleto(s) cancelado(s) con éxito! El stock ha sido liberado.');
}


}