@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- ALERTA DE ERROR / EXPIRACIÓN (Colocada arriba del todo para que destaque) -->
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        <!-- ENCABEZADO -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    🎟️ Mis Boletos Apartados y Activos
                </h2>
                <p class="text-xs text-gray-500 mt-1">Haz clic sobre cualquier boleto válido para ver su código QR.</p>
            </div>
            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full">
                💡 Haz clic en una tarjeta para ver el QR
            </span>
        </div>

        <!-- LISTADO DE BOLETOS -->
        <div class="space-y-4">
            @forelse($tickets as $ticket)
                
                {{-- 🛑 FILTRO 1: Ocultar completamente los folios temporales TICK- del cliente --}}
                @if(str_starts_with($ticket->ticket_code, 'TICK-'))
                    @continue
                @endif

                {{-- Verificamos si el boleto está cancelado --}}
                @php
                    $isCancelled = $ticket->status === 'cancelled';
                @endphp

                {{-- 🎯 TARJETA: Si está cancelado NO lleva onclick (no abre QR), es opaca y no seleccionable --}}
                <div @if(!$isCancelled) onclick="openQrModal('{{ $ticket->ticket_code }}', '{{ $ticket->event->title ?? 'Evento' }}', '{{ $ticket->ticketType->name ?? 'General' }}')" @endif
                     class="bg-white p-5 rounded-2xl shadow-sm border {{ $isCancelled ? 'border-gray-200 opacity-60 bg-gray-50/50 cursor-not-allowed' : 'border-gray-100 cursor-pointer hover:border-indigo-300 hover:shadow-md' }} flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition group">
                    
                    <div>
                        <span class="text-xs font-mono font-bold {{ $isCancelled ? 'text-gray-400 bg-gray-200' : 'text-indigo-600 bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white' }} px-2.5 py-1 rounded-md transition">
                            FOLIO: {{ $ticket->ticket_code }}
                        </span>
                        <h4 class="font-bold {{ $isCancelled ? 'text-gray-500' : 'text-gray-800 group-hover:text-indigo-600' }} text-lg mt-2 transition">
                            {{ $ticket->event->title ?? 'Evento' }}
                        </h4>
                        <p class="text-xs text-gray-400 mt-1">
                            Tipo: <span class="font-semibold text-gray-600">{{ $ticket->ticketType->name ?? 'General' }}</span> 
                            | Cantidad: <span class="font-semibold {{ $isCancelled ? 'text-gray-500' : 'text-indigo-600' }}">Cantidad: {{ $ticket->quantity }} Boleto(s)</span>
                            @if($ticket->event && $ticket->event->date)
                                | Fecha: <span class="text-gray-500">{{ \Carbon\Carbon::parse($ticket->event->date)->format('d/m/Y H:i') }}</span>
                            @endif
                        </p>
                    </div>

                   <div class="flex items-center gap-3" onclick="event.stopPropagation();">
    
                        {{-- 🏷️ ETIQUETA DEL MÉTODO DE PAGO (Independiente) --}}
                        @if($ticket->payment_method === 'cash_on_delivery')
                            <span class="bg-orange-50 text-orange-600 text-xs font-medium px-3 py-1 rounded-full">
                                Pago contra entrega
                            </span>
                        @elseif($ticket->payment_method === 'card')
                            <span class="bg-indigo-50 text-indigo-600 text-xs font-medium px-3 py-1 rounded-full">
                                Tarjeta
                            </span>
                        @endif

                        {{-- 🏷️ ETIQUETA DEL ESTATUS VISUAL (Limpia, sin texto extra) --}}
                        @if($isCancelled)
                            <span class="bg-red-50 text-red-500 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Cancelado
                            </span>
                        @elseif($ticket->status === 'paid')
                            <span class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Pagado
                            </span>
                        @elseif($ticket->status === 'reserved')
                            <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Reservado
                            </span>
                        @elseif($ticket->status === 'paid_pending')
                            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Pago Pendiente
                            </span>
                        @elseif($ticket->status === 'liquidate')
                            <span class="bg-purple-50 text-purple-600 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                Liquidado / Usado
                            </span>
                        @endif

                        {{-- Botón de cancelar manual solo para los pendientes o reservados vigentes --}}
                        @if(in_array($ticket->status, ['paid_pending', 'reserved']))
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('¿Deseas cancelar este boleto?')">
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
                    <p class="text-gray-400 font-medium">No tienes boletos en este momento.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- 📱 MODAL PARA MOSTRAR EL CÓDIGO QR -->
<div id="qrModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl space-y-4">
        <div>
            <h3 id="modalEventTitle" class="font-bold text-lg text-gray-900"></h3>
            <p id="modalTicketType" class="text-xs text-indigo-600 font-semibold"></p>
        </div>

        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex justify-center items-center">
            <!-- Renderizado dinámico del QR -->
            <img id="qrImage" src="" alt="Código QR" class="w-48 h-48 rounded-lg shadow-sm">
        </div>

        <p id="modalFolio" class="font-mono text-xs font-bold text-gray-500"></p>

        <button onclick="closeQrModal()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
            Cerrar
        </button>
    </div>
</div>

<!-- ⚙️ SCRIPT PARA MANEJAR EL MODAL Y GENERAR EL QR -->
<script>
    function openQrModal(folio, eventTitle, ticketType) {
        document.getElementById('modalEventTitle').innerText = eventTitle;
        document.getElementById('modalTicketType').innerText = ticketType;
        document.getElementById('modalFolio').innerText = 'FOLIO: ' + folio;
        
        document.getElementById('qrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(folio)}`;
        
        const modal = document.getElementById('qrModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeQrModal() {
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection