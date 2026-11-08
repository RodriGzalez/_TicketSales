@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- ENCABEZADO Y BOTÓN DE REGRESO -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <!-- Lado Izquierdo: Volver y Título con el Folio -->
            <div>
                <a href="{{ route('admin.events.tickets.global') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 mb-2">
                    ← Volver al listado
                </a>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        🔍 Detalle de Transacción
                    </h1>
                    <span class="bg-indigo-50 text-indigo-700 font-mono text-sm font-bold px-3 py-1 rounded-lg border border-indigo-100">
                        {{ $ticket->ticket_code }}
                    </span>
                </div>
            </div>

            <!-- Lado Derecho: Estatus y Botones de Acción -->
            <div class="flex flex-wrap items-center gap-3">
                @if($ticket->status === 'paid' || $ticket->status === 'liquidate')
                    <span class="bg-emerald-50 text-emerald-700 font-bold px-4 py-2 rounded-xl text-sm border border-emerald-100">
                        ✨ Pagado / Válido
                    </span>
                @elseif($ticket->status === 'reserved')
                    <span class="bg-amber-50 text-amber-700 font-bold px-4 py-2 rounded-xl text-sm border border-amber-100">
                        ⏳ Reservado en Línea
                    </span>
                    <form action="{{ route('admin.events.tickets.markAsPaid', $ticket) }}" method="POST" onsubmit="return confirm('¿Confirmas que se ha recibido el pago de este boleto reservado?');">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5">
                            💵 Cobrar y Marcar como Pagado
                        </button>
                    </form>
                @elseif($ticket->status === 'paid_pending')
                    <span class="bg-blue-50 text-blue-700 font-bold px-4 py-2 rounded-xl text-sm border border-blue-100">
                        📦 Pago Contra Entrega Pendiente
                    </span>
                    <form action="{{ route('admin.events.tickets.markAsPaid', $ticket) }}" method="POST" onsubmit="return confirm('¿Confirmas que se ha recibido el pago en efectivo de este boleto?');">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5">
                            💵 Cobrar y Marcar como Pagado
                        </button>
                    </form>
                @else
                    <span class="bg-red-50 text-red-700 font-bold px-4 py-2 rounded-xl text-sm border border-red-100">
                        ❌ Cancelado / Expirado
                    </span>
                @endif

                {{-- BOTÓN DE CANCELAR: Solo se muestra si NO está pagado, liquidado O cancelado --}}
                @if(!in_array($ticket->status, ['paid', 'liquidate', 'cancelled']))
                    <form action="{{ route('admin.events.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar y eliminar este boleto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-white hover:bg-red-50 text-red-600 text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5 border border-red-200">
                            🗑️ Cancelar Transacción
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- ALERTAS DE SESIÓN -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- GRID DE 3 COLUMNAS PARA LAS TARJETAS PRINCIPALES -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- INFORMACIÓN DEL CLIENTE -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">👤 Comprador</h3>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Nombre</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $ticket->user->name ?? 'Invitado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Correo Electrónico</p>
                    <p class="text-sm font-medium text-gray-600 mt-0.5">{{ $ticket->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">ID de Usuario</p>
                    <p class="text-xs font-mono text-gray-500 mt-0.5">#{{ $ticket->user_id ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- INFORMACIÓN DEL EVENTO Y TICKET -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">🎟️ Evento y Localidad</h3>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Evento</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $ticket->event->title ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Tipo de Boleto</p>
                    <p class="text-sm font-medium text-indigo-600 mt-0.5">{{ $ticket->ticketType->name ?? 'General' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Cantidad Adquirida</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $ticket->quantity ?? 1 }} boleto(s)</p>
                </div>
            </div>

            <!-- DESGLOSE FINANCIERO Y PAGO -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">💳 Desglose de Pago</h3>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Método de Pago</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">
                       @php
                           $method = $ticket->payment_method ?? 'not_specified';
                       @endphp

                        @if($method === 'cash_on_delivery')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                Pago contra entrega
                            </span>
                        @elseif($method === 'card')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">
                                Pago con Tarjeta
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500">
                                No especificado
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Precio Unitario</p>
                    <p class="text-sm font-medium text-gray-600 mt-0.5">${{ number_format($ticket->ticketType->price ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">TOTAL</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">
                        {{-- Tomamos el total real de la orden de la base de datos, con respaldo al cálculo si no existiera --}}
                        ${{ number_format(optional($ticket->order)->total_amount ?? ($ticket->quantity * $ticket->price), 2) }}
                    </p>
                </div>
            </div>

        </div> <!-- 👈 Cierre correcto del grid de 3 columnas -->

        <!-- INFORMACIÓN TÉCNICA Y TRAZABILIDAD (ANCHO COMPLETO FUERA DEL GRID) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4 w-full">
            <h3 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">⚙️ Trazabilidad y Fechas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block">Fecha de Creación / Intento</span>
                    <span class="font-medium text-gray-700">{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i:s') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block">Última Actualización</span>
                    <span class="font-medium text-gray-700">{{ $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Orden Asociada (ID / Folio)</p>
                        @if($ticket->order_id)
                            {{-- Si tiene orden asociada (pagos con tarjeta), mostramos el ID de la orden --}}
                            {{ $ticket->Order->stripe_payment_intent_id}}
                        @else
                            {{-- Si es pago contra entrega, mostramos su propio folio --}}
                            {{ $ticket->ticket_code }}
                        @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection