@extends('layouts.app')

@section('title', $event->title . ' - GraduFest')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Botón Regresar -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 mb-6">
        ← Volver a los eventos
    </a>
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 text-red-700 text-sm rounded-xl border border-red-100 font-medium">
        {{ session('error') }}
    </div>
@endif
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

        <!-- Columna Derecha: Cartelera y Selector de Boletos con Alpine.js (- y +) -->
        <div class="space-y-6" 
             x-data="{ 
                 quantities: { 
                     @foreach($event->ticketTypes as $type)
                         '{{ $type->id }}': 0,
                     @endforeach
                 },
                 maxPerUser: {{ $event->max_tickets_per_user ?? 5 }},
                 getTotal() {
                     return Object.values(this.quantities).reduce((a, b) => a + b, 0);
                 },
                 increment(id, maxStock) {
                     if (this.getTotal() < this.maxPerUser && this.quantities[id] < maxStock) {
                         this.quantities[id]++;
                     }
                 },
                 decrement(id) {
                     if (this.quantities[id] > 0) {
                         this.quantities[id]--;
                     }
                 }
             }">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Selecciona tus boletos</h3>
                <p class="text-xs text-gray-500 mb-6">Máximo {{ $event->max_tickets_per_user }} boletos por persona.</p>

                <form action="{{ route('tickets.store', $event->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    @foreach($event->ticketTypes as $type)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-indigo-300 transition">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $type->name }}</p>
                                <p class="text-indigo-600 font-extrabold text-base">${{ number_format($type->price, 2) }} MXN</p>
                                <span class="text-xs text-green-600 font-medium">Disponible ({{ $type->stock }} restantes)</span>
                            </div>

                            <!-- Botones - y + -->
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                    @click="decrement('{{ $type->id }}')"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 font-bold hover:bg-gray-100 transition active:scale-95 shadow-sm">
                                    -
                                </button>
                                
                                <span class="w-6 text-center font-bold text-gray-900 text-base" x-text="quantities['{{ $type->id }}']">0</span>
                                
                                <button type="button" 
                                    @click="increment('{{ $type->id }}', {{ $type->stock }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 font-bold hover:bg-gray-100 transition active:scale-95 shadow-sm">
                                    +
                                </button>
                            </div>

                            <!-- Input oculto para enviar los datos al controlador -->
                            <input type="hidden" name="tickets[{{ $type->id }}]" :value="quantities['{{ $type->id }}']">
                        </div>
                    @endforeach

                    <!-- Resumen rápido -->
                    <div class="pt-4 border-t border-gray-100 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 font-medium">Total seleccionados:</span>
                            <span class="font-bold text-indigo-600" x-text="getTotal() + ' / ' + maxPerUser + ' boletos'"></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tiempo de apartado:</span>
                            <span class="font-semibold text-amber-600">⏱️ {{ $event->reservation_expiry_time }} minutos</span>
                        </div>
                    </div>

                    <button type="submit" 
                        :disabled="getTotal() === 0"
                        :class="getTotal() === 0 ? 'opacity-50 cursor-not-allowed bg-indigo-400' : 'hover:bg-indigo-700 shadow-indigo-600/30'"
                        class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl transition shadow-lg">
                        Apartar y Continuar
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection