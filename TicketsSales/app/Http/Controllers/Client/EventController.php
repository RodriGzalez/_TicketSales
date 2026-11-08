<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Buscamos los eventos activos en la base de datos
        $events = Event::where('status', 'active')->get();

        // Retornamos una vista llamada 'events.index' enviándole los eventos
        return view('events.index', compact('events'));
    }
    
   public function show($id) // O usando Route Model Binding: public function show(Event $event)
{
    // Si estás recibiendo el ID manualmente:
    $event = \App\Models\Event::with('ticketTypes')->findOrFail($id);

    // Si usas Route Model Binding, asegúrate de que esté así:
    // $event->load('ticketTypes');

    // 🧮 Tu lógica de cálculo de stock...
    $totalStock = $event->ticketTypes->sum('stock');
    
    $totalOcupados = \App\Models\OrderItem::whereHas('order', function($q) use ($event) {
        $q->where('event_id', $event->id)
          ->where(function($sub) {
              $sub->where('status', 'paid')
                  ->orWhere('status', 'liquidate')
                  ->orWhere(fn($p) => $p->where('status', 'pending')->where('expires_at', '>', now()));
          });
    })->sum('quantity');

    $stockDisponibleGlobal = max(0, $totalStock - $totalOcupados);

    // 🛑 VERIFICA ESTA LÍNEA: El nombre de la variable debe coincidir exactamente
    return view('events.show', compact('event', 'stockDisponibleGlobal'));
}
public function myTickets()
{
    $user = auth()->user();

    $tickets = \App\Models\Ticket::where('user_id', $user->id)
        ->with(['event', 'ticketType'])
        ->whereIn('status', ['paid', 'liquidate', 'paid_pending', 'reserved'])
        ->where('ticket_code', 'NOT LIKE', 'TICK-%') // Asegura excluir cualquier TICK-
        ->latest()
        ->get();

    return view('events.tickets', compact('tickets'));
}
}