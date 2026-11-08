<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Order;

class EventController extends Controller
{
    // 📊 Control de Stock por Tipo de Boleto (Tu método original intacto)
    public function index() 
    {
        $events = Event::with(['ticketTypes' => function ($query) {
            $query->withCount([
                'tickets as reserved_count' => function ($q) {
                    $q->where('status', 'reserved')
                      ->where('ticket_code', 'NOT LIKE', 'TICK-%');
                },
                'tickets as paid_pending_count' => function ($q) {
                    $q->where('status', 'paid_pending');
                },
                'tickets as paid_count' => function ($q) {
                    $q->whereIn('status', ['paid', 'liquidate']);
                }
            ]);
        }])->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
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

        $ticketTypes = $validated['ticket_types'];
        unset($validated['ticket_types']);

        $event = Event::create($validated);

        foreach ($ticketTypes as $type) {
            $event->ticketTypes()->create([
                'name' => $type['name'],
                'price' => $type['price'],
                'stock' => $type['stock'],
            ]);
        }

        return redirect()->route('admin.events.index')->with('success', '¡Evento y tipos de boletos creados con éxito!');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
{
    // 1. Validar utilizando los nombres exactos de los inputs del formulario
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'required', // 👈 Coincide con name="date"
        'location' => 'required|string',
        'total_capacity' => 'required|integer|min:1', // 👈 Coincide con name="total_capacity"
        'max_tickets_per_user' => 'required|integer|min:1',
        'reservation_expiry_time' => 'required|integer|min:1', // 👈 Coincide con name="reservation_expiry_time"
        'image_url' => 'nullable|url',
        'status' => 'required|string',
    ]);

    // 2. Actualizar los datos del evento
    $event->update($validated);

    // 3. 🔄 SINCRONIZAR EL STOCK: Actualizar automáticamente el stock del tipo de boleto principal
    $ticketType = $event->ticketTypes()->first();
    if ($ticketType) {
        $ticketType->update([
            'stock' => $validated['total_capacity'] // Asignamos la nueva capacidad total al stock del boleto
        ]);
    } else {
        // Si por alguna razón el evento no tiene un tipo de boleto creado, lo creamos por defecto
        \App\Models\TicketType::create([
            'event_id' => $event->id,
            'name' => 'General',
            'price' => 0, // Puedes ajustar el precio por defecto si aplica
            'stock' => $validated['total_capacity']
        ]);
    }

            return redirect()->route('admin.events.index')
                ->with('success', '¡Evento actualizados correctamente!');
}

    public function destroy(Event $event)
    {
        $event->update([
            'status' => 'archived'
        ]);

        return redirect()->route('admin.events.index')->with('success', '¡El evento ha sido archivado exitosamente!');
    }

    public function show(Event $event)
    {
        // 🧹 Limpieza automática de órdenes vencidas
        $expiredOrders = Order::where('event_id', $event->id)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            Ticket::where('order_id', $order->id)
                ->where('status', 'reserved')
                ->update(['status' => 'cancelled']);

            $order->update(['status' => 'expired']);
        }

        // 📊 Cargamos los tipos de boleto sumando la cantidad real de los OrderItems pendientes vigentes
        $event->load(['ticketTypes' => function ($query) use ($event) {
            $query->withCount([
                'orderItems as reserved_count' => function ($q) use ($event) {
                    $q->whereHas('order', function ($orderQuery) use ($event) {
                        $orderQuery->where('event_id', $event->id)
                                   ->where('status', 'pending')
                                   ->where('expires_at', '>', now());
                    });
                },
                'tickets as paid_count' => fn($q) => $q->where('status', 'paid'),
                'tickets as liquidate_count' => fn($q) => $q->where('status', 'liquidate'),
            ]);
        }]);

        return view('admin.events.show', compact('event'));
    }

    // 📋 Método de Reporte de Transacciones y Órdenes
   public function ticketReport(Request $request, Event $event = null)
    {
        $events = Event::all();

        // 🧹 1. Limpieza automática de órdenes pendientes expiradas
        \App\Models\Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        // 📊 2. Consulta basada en TICKETS oficiales de forma limpia
        if ($event && $event->exists) {
            $query = Ticket::where('event_id', $event->id)->with(['event', 'user', 'ticketType', 'order']);
            $baseQuery = Ticket::where('event_id', $event->id);
        } else {
            $query = Ticket::with(['event', 'user', 'ticketType', 'order']);
            $baseQuery = Ticket::query();
        }

        // 🛑 FILTRO POR DEFECTO: Ocultar los 'reserved' a menos que se filtre explícitamente por ellos
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', '!=', 'reserved');
        }
        // 🔍 3. Filtros por buscador
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('ticket_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            if ($paymentMethod === 'card') {
                $query->where('payment_method', 'card');
            } elseif ($paymentMethod === 'cash') {
                $query->where('payment_method', 'cash_on_delivery');
            }
        }

        $tickets = $query->latest()->paginate(15)->appends($request->query());

        // 📈 4. Estadísticas consolidadas sumando la cantidad correcta
        $stats = [
            'total_sold'   => (clone $baseQuery)->whereIn('status', ['paid', 'paid_pending', 'liquidate'])->sum('quantity'),
            // Para reservados, contamos los items de órdenes pendientes vigentes
            'reserved'     => \App\Models\OrderItem::whereHas('order', function($q) use ($event) {
                                  if($event) $q->where('event_id', $event->id);
                                  $q->where('status', 'pending')->where('expires_at', '>', now());
                              })->sum('quantity'),
            'paid_pending' => (clone $baseQuery)->where('status', 'paid_pending')->sum('quantity'),
            'paid'         => (clone $baseQuery)->whereIn('status', ['paid', 'liquidate'])->sum('quantity'),
            'cancelled'    => (clone $baseQuery)->whereIn('status', ['cancelled', 'expired'])->count(),
            'total_revenue'=> (clone $baseQuery)->whereIn('status', ['paid', 'liquidate'])->get()->sum(function($t) {
                return $t->order ? $t->order->total_amount : ($t->quantity * ($t->ticketType->price ?? 0));
            }),
        ];

        return view('admin.events.tickets', [
            'event' => $event,
            'events' => $events,
            'tickets' => $tickets, // Devolvemos la colección de tickets que espera tu vista
            'stats' => $stats
        ]);
    }

    public function globalTicketReport()
    {
        $event = Event::first(); 
        
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

    public function showTicketDetail(Ticket $ticket)
    {
        $ticket->load(['user', 'ticketType', 'event', 'order']);
        return view('admin.events.ticket-detail', compact('ticket'));
    }

   public function markTicketAsPaid(Ticket $ticket)
    {
        if (in_array($ticket->status, ['paid_pending', 'reserved'])) {
            // 1. Actualizamos el ticket
            $ticket->update([
                'status' => 'paid',
                'payment_method' => $ticket->payment_method === 'not_specified' ? 'cash_on_delivery' : $ticket->payment_method
            ]);

            // 💡 2. ¡IMPORTANTE! Actualizamos también la Orden asociada para que pase de 'pending' a 'paid'
            if ($ticket->order_id) {
                \App\Models\Order::where('id', $ticket->order_id)->update([
                    'status' => 'paid'
                ]);
            }

            return back()->with('success', '¡El boleto y su orden han sido marcados como pagados exitosamente!');
        }

        return back()->with('error', 'Este ticket no se puede marcar como pagado.');
    }

    public function destroyTicket(Ticket $ticket)
    {
        if (in_array($ticket->status, ['paid', 'liquidate'])) {
            return back()->with('error', 'No se puede cancelar un boleto que ya ha sido pagado.');
        }

        $ticket->delete();

        return redirect()->route('admin.events.tickets.global')->with('success', 'Transacción cancelada correctamente.');
    }

    public function ticketsIndex(Event $event = null)
    {
        $query = Ticket::with(['order', 'ticketType', 'user']);

        if ($event) {
            $query->where('event_id', $event->id);
        }

        $tickets = $query->latest()->paginate(10);

        return view('admin.events.tickets', compact('tickets', 'event'));
    }
}