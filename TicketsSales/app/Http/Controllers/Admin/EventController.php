<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // 🧹 PASO 3: Limpieza automática de órdenes vencidas
        $expiredOrders = \App\Models\Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            // Candado estricto: Solo borramos los que están en proceso de compra ('reserved')
            // Los 'paid_pending' (pago contra entrega) se quedan intactos
            \App\Models\Ticket::where('order_id', $order->id)
                ->where('status', 'reserved') 
                ->delete(); 

            // Actualizamos la orden como expirada
            $order->update(['status' => 'expired']);
        }

        $events = \App\Models\Event::latest()->get();
        return view('admin.events.index', compact('events'));
    }
    // 2. Mostrar formulario de creación
    public function create()
    {
        return view('admin.events.create');
    }

    // 3. Guardar el nuevo evento
    public function store(Request $request)
{
    // 1. Validar los datos
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'date' => 'required|date',
        'location' => 'required|string',
        'total_capacity' => 'required|integer',
        'max_tickets_per_user' => 'required|integer',
        'reservation_expiry_time' => 'required|integer',
        'status' => 'required|in:active,inactive,archived',
        'image_url' => 'nullable|string',
        'ticket_types' => 'required|array|min:1',
        'ticket_types.*.name' => 'required|string',
        'ticket_types.*.price' => 'required|numeric|min:0',
        'ticket_types.*.stock' => 'required|integer|min:1',
    ]);

    // 2. Extraer los tipos de boletos y dejar solo los datos del evento
    $ticketTypes = $validated['ticket_types'];
    unset($validated['ticket_types']);

    // 3. Crear el evento limpiamente
    $event = Event::create($validated);

    // 4. Recorrer y crear los tipos de boletos asociados al nuevo evento
    foreach ($ticketTypes as $type) {
        $event->ticketTypes()->create([
            'name' => $type['name'],
            'price' => $type['price'],
            'stock' => $type['stock'],
        ]);
    }

    return redirect()->route('admin.events.index')->with('success', '¡Evento y tipos de boletos creados con éxito!');
}

    // 4. Mostrar formulario de edición
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // 5. Actualizar el evento
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'image_url' => 'nullable|url',
            'total_capacity' => 'required|integer|min:1',
            'max_tickets_per_user' => 'required|integer|min:1',
            'reservation_expiry_time' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,archived',
    ]);

        $event->update($validated);


        return redirect()->route('admin.events.index')->with('success', '¡Evento actualizado correctamente!');
    }

    // 6. Archivado lógico (Sustituye al Delete físico)
    public function destroy(Event $event)
    {
        // En lugar de hacer $event->delete(), cambiamos su estatus a archivado
        $event->update([
            'status' => 'archived'
        ]);

        return redirect()->route('admin.events.index')->with('success', '¡El evento ha sido archivado exitosamente!');
    }

    public function show(Event $event)
        {
            // 🧹 1. LIMPIEZA AUTOMÁTICA DE ÓRDENES VENCIDAS
            // Buscamos órdenes pendientes de este evento que ya hayan superado su tiempo límite
            $expiredOrders = \App\Models\Order::where('event_id', $event->id)
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->get();

            foreach ($expiredOrders as $order) {
                // Borramos físicamente los tickets temporales de la reserva para devolver el stock
                \App\Models\Ticket::where('order_id', $order->id)
                    ->where('status', 'reserved')
                    ->delete();

                // Actualizamos la orden como expirada
                $order->update(['status' => 'expired']);
            }

            // 2. Cargar los tipos de boleto calculando los ocupados
            $event->load(['ticketTypes' => function ($query) {
            $query->withCount([
                'tickets as reserved_count' => fn($q) => $q->where('status', 'reserved'),
                'tickets as paid_pending_count' => fn($q) => $q->where('status', 'paid_pending'),
                'tickets as paid_count' => fn($q) => $q->where('status', 'paid'),
                'tickets as liquidate_count' => fn($q) => $q->where('status', 'liquidate'),
            ]);
        }]);

            return view('admin.events.show', compact('event'));
        }

    public function ticketReport(\App\Models\Event $event)
    {
        $events = \App\Models\Event::all();

        // Cargamos los tipos de boleto del evento con sus conteos en tiempo real
        $event->load(['ticketTypes' => function ($query) use ($event) {
            $query->withCount([
                'tickets as reserved_count' => function ($q) {
                    $q->where('status', 'reserved');
                },
                'tickets as paid_count' => function ($q) {
                    $q->where('status', 'paid');
                }
            ]);
        }]);

        $tickets = $event->tickets()->with(['user', 'ticketType'])->latest()->paginate(15);

        // Calculamos estadísticas generales
        $stats = [
            'total_sold'    => $event->tickets()->where('status', '!=', 'cancelled')->sum('quantity'),
            'reserved'      => $event->tickets()->where('status', 'reserved')->sum('quantity'),
            'pending_payment' => $event->tickets()->where('status', 'pending_payment')->sum('quantity'), // 👈 Nuevo estatus provisional
            'paid'          => $event->tickets()->where('status', 'paid')->sum('quantity'),
            'cancelled'     => $event->tickets()->where('status', 'cancelled')->sum('quantity'),
            
            'total_revenue' => $event->tickets()
                ->where('status', '!=', 'cancelled')
                ->get()
                ->sum(function($ticket) {
                    return ($ticket->quantity ?? 1) * ($ticket->ticketType->price ?? 0);
                }),
        ];

        return view('admin.events.tickets', compact('event', 'events', 'tickets', 'stats'));
    }

    public function globalTicketReport()
{
    $event = \App\Models\Event::first(); // Toma el primer evento por defecto
    
    if (!$event) {
        return view('admin.events.tickets', [
            'event' => null, 
            'events' => collect(), 
            'tickets' => collect(), 
            'stats' => ['total_sold' => 0, 'reserved' => 0, 'paid' => 0, 'cancelled' => 0]
        ]);
    }

    return redirect()->route('admin.events.tickets', $event);
}



}