<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // 1. Listar todos los eventos (ordenados del más reciente)
    public function index()
    {
        $events = Event::latest()->get();
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
            // Cargar los tipos de boleto sumando la cantidad de los boletos que NO estén cancelados
            $event->load(['ticketTypes' => function ($query) {
                $query->withCount(['tickets as sold_count' => function ($q) {
                    $q->where('status', '!=', 'cancelled');
                }]);
            }]);

            return view('events.show', compact('event'));
        }
    public function ticketReport(\App\Models\Event $event)
{
    $events = \App\Models\Event::all();

    $tickets = $event->tickets()->with(['user', 'ticketType'])->latest()->paginate(15);

    // Calculamos estadísticas incluyendo ingresos
    $stats = [
        'total_sold' => $event->tickets()->where('status', '!=', 'cancelled')->sum('quantity'),
        'reserved'   => $event->tickets()->where('status', 'reserved')->sum('quantity'),
        'paid'       => $event->tickets()->where('status', 'paid')->sum('quantity'),
        'cancelled'  => $event->tickets()->where('status', 'cancelled')->sum('quantity'),
        
        // Suma total de ingresos (cantidad * precio del ticket type)
        'total_revenue' => $event->tickets()
            ->where('status', '!=', 'cancelled')
            ->get()
            ->sum(function($ticket) {
                return $ticket->quantity * ($ticket->ticketType->price ?? 0);
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