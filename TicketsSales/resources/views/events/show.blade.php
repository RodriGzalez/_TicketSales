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

        <!-- Columna Derecha: Selector de Boletos con Alpine.js Único -->
        <div class="space-y-6" 
             x-data="{ 
                 tickets: {
                     @foreach($event->ticketTypes as $type)
                         '{{ $type->id }}': 0{{ $loop->last ? '' : ',' }}
                     @endforeach
                 },
                 maxPerUser: {{ $event->max_tickets_per_user ?? 5 }},
                 getTotal() {
                     return Object.values(this.tickets).reduce((a, b) => a + Number(b), 0);
                 }
             }">
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Selecciona tus boletos</h3>
                <p class="text-xs text-gray-500 mb-6">Máximo {{ $event->max_tickets_per_user }} boletos por persona.</p>

               <form action="{{ route('orders.store', $event->id) }}" method="POST" class="space-y-4">
                    
                    @foreach($event->ticketTypes as $type)
                        @php
                            $soldCount = $type->sold_count ?? 0;
                            $remainingStock = $type->stock - $soldCount;
                        @endphp

                        <div class="border border-gray-200 rounded-xl p-4 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $type->name }}</h4>
                                <p class="text-xs text-indigo-600 font-semibold">${{ number_format($type->price, 2) }} MXN</p>
                                
                                <p class="text-xs {{ $remainingStock > 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">
                                    {{ $remainingStock > 0 ? "Disponibles: {$remainingStock} restantes" : '¡Agotado!' }}
                                </p>
                            </div>

                            <!-- Controles de cantidad -->
                            <div class="flex items-center gap-3">
                                <button type="button" 
                                        @click="if(tickets['{{ $type->id }}'] > 0) tickets['{{ $type->id }}']--" 
                                        class="w-8 h-8 rounded-lg bg-gray-100 font-bold hover:bg-gray-200">-</button>
                                
                                <span x-text="tickets['{{ $type->id }}']" class="font-bold text-sm w-4 text-center"></span>
                                
                                <button type="button" 
                                        @click="if(tickets['{{ $type->id }}'] < {{ $remainingStock }} && getTotal() < maxPerUser) tickets['{{ $type->id }}']++" 
                                        class="w-8 h-8 rounded-lg bg-gray-100 font-bold hover:bg-gray-200">+</button>
                                
                                <!-- Input oculto que viaja al controlador -->
                                <input type="hidden" name="tickets[{{ $type->id }}]" x-model="tickets['{{ $type->id }}']">
                            </div>
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