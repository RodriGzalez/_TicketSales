@extends('layouts.app') {{-- O el layout principal que utilice tu panel de usuario --}}

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- ENCABEZADO -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                🎟️ Mis Boletos Apartados y Activos
            </h2>
            <p class="text-xs text-gray-500 mt-1">Aquí puedes ver tus boletos válidos, pagados o pendientes de pago contra entrega.</p>
        </div>

        <!-- LISTADO DE BOLETOS -->
        <div class="space-y-4">
            @forelse($tickets as $ticket)
                
                {{-- 🛑 FILTRO DE SEGURIDAD EN FRONT: Ocultar si está reservado, cancelado o expirado --}}
                @if(in_array($ticket->status, ['reserved', 'cancelled', 'expired']))
                    @continue
                @endif

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">
                            FOLIO: {{ $ticket->ticket_code }}
                        </span>
                        <h4 class="font-bold text-gray-800 text-lg mt-2">
                            {{ $ticket->event->title ?? 'Evento' }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-1">
                            Tipo: <span class="font-semibold text-gray-700">{{ $ticket->ticketType->name ?? 'General' }}</span> 
                            | Cantidad: <span class="font-semibold text-indigo-600">1 Boleto</span>
                            @if($ticket->event && $ticket->event->date)
                                | Fecha: <span class="text-gray-600">{{ \Carbon\Carbon::parse($ticket->event->date)->format('d/m/Y H:i') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Estatus visual permitido en el front --}}
                        @if($ticket->status === 'paid')
                            <span class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Pagado (Tarjeta)
                            </span>
                        @elseif($ticket->status === 'paid_pending')
                            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Pago Pendiente (Contra Entrega)
                            </span>
                        @elseif($ticket->status === 'liquidate')
                            <span class="bg-purple-50 text-purple-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Liquidado / Usado
                            </span>
                        @endif

                        {{-- Botón opcional para cancelar manualmente el pago contra entrega si lo deseas --}}
                        @if($ticket->status === 'paid_pending')
                            <form action="{{ route('tickets.cancel', $ticket->id) }}" method="POST" onsubmit="return confirm('¿Deseas cancelar este boleto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                    Cancelar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            @empty
                <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-gray-400 font-medium">No tienes boletos activos o apartados en este momento.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection