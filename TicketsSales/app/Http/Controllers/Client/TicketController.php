<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketController extends Controller
{
public function store(Request $request, Event $event)
{
    // 1. Validar que el evento esté activo
    if ($event->status !== 'active') {
        return back()->with('error', 'Este evento no está disponible para boletos.');
    }

    $maxLimit = (int) $event->max_tickets_per_user;

    // 2. OBTENER CORRECTAMENTE EL TOTAL DE BOLETOS SELECCIONADOS EN ALPINE.JS
    // El formato que llega es: $request->input('tickets') => [ '1' => 3, '2' => 2 ]
    $ticketInputs = $request->input('tickets', []);
    $requestedTotal = 0;

    if (is_array($ticketInputs)) {
        foreach ($ticketInputs as $typeId => $qty) {
            $requestedTotal += (int) $qty; // Sumamos estrictamente los valores (las cantidades)
        }
    }

    // Si el usuario no seleccionó nada
    if ($requestedTotal <= 0) {
        return back()->with('error', 'Debes seleccionar al menos un boleto.');
    }

    // 3. Sumar cuántos boletos activos ya tiene el usuario para este evento
    $userTicketsCount = (int) $event->tickets()
        ->where('user_id', auth()->id())
        ->where('status', '!=', 'cancelled')
        ->sum('quantity');

    // 4. VALIDACIÓN ESTRICTA DE LÍMITE POR USUARIO
    if (($userTicketsCount + $requestedTotal) > $maxLimit) {
        $disponiblesParaUsuario = $maxLimit - $userTicketsCount;

        if ($disponiblesParaUsuario <= 0) {
            return back()->with('error', 'Límite alcanzado: Ya tienes ' . $userTicketsCount . ' boletos activos para este evento.');
        }

        return back()->with('error', 'Solo puedes apartar ' . $disponiblesParaUsuario . ' boleto(s) más para respetar el límite de este evento.');
    }

    // 5. Validar capacidad total del evento
    $soldTotal = (int) $event->tickets()->where('status', '!=', 'cancelled')->sum('quantity');
    if (($soldTotal + $requestedTotal) > (int) $event->total_capacity) {
        return back()->with('error', 'Lo sentimos, no hay suficientes boletos disponibles en total.');
    }

    // 6. CREAR EL FOLIO ÚNICO CON LA CANTIDAD REAL SELECCIONADA
    \App\Models\Ticket::create([
        'user_id' => auth()->id(),
        'event_id' => $event->id,
        'quantity' => $requestedTotal, // ¡Aquí se guardará el 5 real en lugar de 1!
        'ticket_code' => 'GF-' . strtoupper(\Illuminate\Support\Str::random(8)),
        'status' => 'reserved',
        'expires_at' => \Carbon\Carbon::now()->addMinutes($event->reservation_expiry_time),
    ]);

    return redirect()->route('dashboard')->with('success', '¡Orden de boletos apartada con éxito bajo un mismo folio!');
}

public function destroy(\App\Models\Ticket $ticket)
{
    // Seguridad: verificar que el ticket pertenezca al usuario logueado
    if ($ticket->user_id !== auth()->id()) {
        abort(403);
    }

    // Cambiar estatus a cancelado para liberar el cupo y el límite del usuario
    $ticket->update(['status' => 'cancelled']);

    return redirect()->route('dashboard')->with('success', '¡Boleto(s) cancelado(s) con éxito! Tu límite ha sido liberado.');
}
}