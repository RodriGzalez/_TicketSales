@extends('layouts.app')

@section('title', $event->title . ' - GraduFest')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Botón Regresar -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 mb-6">
        ← Volver a los eventos
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Columna Izquierda/Centro: Información del Evento -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-80 object-cover">
                <div class="p-6 md:p-8">
                    <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">
                        Evento Confirmado
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ $event->title }}</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6 p-4 bg-gray-50 rounded-xl text-sm">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📅</span>
                            <div>
                                <p class="font-bold text-gray-900">Fecha y Hora</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->date)->format('d \d\e F, Y - h:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📍</span>
                            <div>
                                <p class="font-bold text-gray-900">Ubicación</p>
                                <p class="text-gray-600">{{ $event->location }}</p>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-2">Acerca del evento</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $event->description }}</p>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Cartelera y Selector de Boletos -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Selecciona tus boletos</h3>
                <p class="text-xs text-gray-500 mb-6">Máximo {{ $event->max_tickets_per_user }} boletos por persona.</p>

                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    
                    @foreach($event->ticketTypes as $type)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-indigo-300 transition">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $type->name }}</p>
                                <p class="text-indigo-600 font-extrabold text-base">${{ number_format($type->price, 2) }} MXN</p>
                                <span class="text-xs text-green-600 font-medium">Disponible ({{ $type->stock }} restantes)</span>
                            </div>

                            <!-- Selector de Cantidad -->
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50">
                                <select name="tickets[{{ $type->id }}]" class="bg-transparent text-sm font-bold text-gray-800 px-3 py-2 outline-none cursor-pointer">
                                    <option value="0">0</option>
                                    @for($i = 1; $i <= min($event->max_tickets_per_user, $type->stock); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @endforeach

                    <!-- Resumen rápido -->
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tiempo de apartado:</span>
                            <span class="font-semibold text-amber-600">⏱️ {{ $event->reservation_expiry_time }} minutos</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-indigo-600/30">
                        Apartar y Continuar
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection