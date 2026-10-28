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
}