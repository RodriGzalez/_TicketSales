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

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', '¡Evento creado exitosamente!');
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
}