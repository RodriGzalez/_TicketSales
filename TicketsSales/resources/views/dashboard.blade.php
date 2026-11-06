<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Contenedor Alpine.js -->
    <div class="py-12" 
         x-data="{ 
             modalOpen: false, 
             selectedTicket: {},
             showTicket(ticket) {
                 this.selectedTicket = ticket;
                 this.modalOpen = true;
             }
         }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-100 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Mis Boletos Apartados</h3>
                    <span class="text-xs text-indigo-600 font-semibold">💡 Haz clic sobre cualquier boleto válido para ver su código QR</span>
                </div>

                <div class="space-y-4">
                    @forelse($tickets as $ticket)
                        @php
                            $isCancelled = $ticket->status === 'cancelled';
                            $qrSvg = !$isCancelled ? SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($ticket->ticket_code) : null;
                        @endphp

                        <!-- Tarjeta interactiva o estática según el estatus -->
                        <div @if(!$isCancelled) 
                                @click="showTicket({
                                    code: '{{ $ticket->ticket_code }}',
                                    quantity: '{{ $ticket->quantity }}',
                                    title: @js($ticket->event->title),
                                    location: @js($ticket->event->location),
                                    date: '{{ \Carbon\Carbon::parse($ticket->event->date)->format('d/m/Y H:i') }}',
                                    typeName: @js($ticket->ticketType->name ?? 'General'),
                                    qrSvg: @js($qrSvg)
                                })"
                                class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 cursor-pointer hover:border-indigo-400 hover:shadow-md transition"
                             @else
                                class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50 opacity-60"
                             @endif>
                            
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                                        Folio: {{ $ticket->ticket_code }}
                                    </span>
                                    <span class="text-xs font-extrabold bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full">
                                        {{ $ticket->quantity }} {{ $ticket->quantity == 1 ? 'Boleto' : 'Boletos' }}
                                    </span>
                                </div>

                                <h4 class="text-base font-bold text-gray-900 mt-1">{{ $ticket->event->title }}</h4>
                                <p class="text-xs text-gray-500">Lugar: {{ $ticket->event->location }} | Fecha: {{ \Carbon\Carbon::parse($ticket->event->date)->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                                <div>
                                    <span class="block text-xs text-gray-400 mb-1">Estatus</span>
                                    @if($ticket->status === 'reserved')
                                        <span class="text-xs font-bold bg-amber-50 text-amber-600 px-2.5 py-1 rounded-md">Pago Pendiente (Contra Entrega)</span>
                                    @elseif($ticket->status === 'paid')
                                        <span class="text-xs font-bold bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-md">Pagado</span>
                                    @else
                                        <span class="text-xs font-bold bg-red-50 text-red-600 px-2.5 py-1 rounded-md">Cancelado</span>
                                    @endif
                                </div>

                                <!-- Botón de Cancelar (solo si está reservado) -->
                                @if($ticket->status === 'reserved')
                                    <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" @click.stop onsubmit="return confirm('¿Estás seguro de cancelar este apartado? Se liberarán tus boletos.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-3 py-2 rounded-xl text-xs transition border border-red-200">
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aún no tienes boletos apartados. ¡Explora los eventos y aparta el tuyo!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- VENTANA MODAL (POPUP) PARA MOSTRAR EL QR -->
        <div x-show="modalOpen" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             x-transition.opacity
             style="display: none;">
            
            <div @click.away="modalOpen = false" 
                 class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 relative transform transition-all">
                
                <!-- Botón Cerrar (X) -->
                <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-xl">
                    ✕
                </button>

                <!-- Encabezado Modal -->
                <div class="text-center mb-6">
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase" x-text="selectedTicket.typeName"></span>
                    <h3 class="font-extrabold text-xl text-gray-900 mt-2" x-text="selectedTicket.title"></h3>
                    <p class="text-xs text-gray-500 mt-1" x-text="'Lugar: ' + selectedTicket.location"></p>
                    <p class="text-xs text-indigo-600 font-semibold mt-0.5" x-text="'Fecha: ' + selectedTicket.date"></p>
                </div>

                <!-- Contenedor del Código QR generado -->
                <div class="flex flex-col items-center justify-center bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-3">
                        Folio: <span x-text="selectedTicket.code" class="text-gray-700 font-mono"></span>
                    </span>
                    
                    <!-- SVG renderizado dinámicamente -->
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200" x-html="selectedTicket.qrSvg"></div>
                    
                    <p class="text-[11px] text-gray-400 text-center mt-4">Presenta este código QR en la entrada del evento para validar tu acceso.</p>
                </div>

                <!-- Botón inferior para cerrar -->
                <div class="mt-6">
                    <button @click="modalOpen = false" class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-gray-800 transition">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>