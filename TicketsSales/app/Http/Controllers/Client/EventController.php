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
    
    public function show($id)
{
    // Cargamos el evento junto con sus tipos de boletos
    $event = Event::with('ticketTypes')->findOrFail($id);

    return view('events.show', compact('event'));
}

public function myTickets()
{
    // Obtenemos los boletos del usuario logueado con sus relaciones
    $tickets = auth()->user()->tickets()->with(['event', 'ticketType'])->latest()->get();

    return view('events.tickets', compact('tickets'));
}
}