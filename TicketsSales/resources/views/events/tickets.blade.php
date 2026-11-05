@extends('layouts.app')

@section('title', 'Mis Boletos - GraduFest')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" 
     x-data="{ 
         openModal: false, 
         selectedTicket: {} 
     }">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            🎟️ Mis Boletos y Accesos
        </h2>
        <p class="text-xs text-gray-500 mt-1">Haz clic sobre cualquier tarjeta de boleto para ver su código QR y detalles de acceso.</p>
    </div>

    <!-- Listado de Tarjetas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tickets as $ticket)
            <div @click="selectedTicket = {
                    title: '{{ addslashes($ticket->event->title ?? 'Evento') }}',
                    location: '{{ addslashes($ticket->event->location ?? 'Por definir') }}',
                    date: '{{ \Carbon\Carbon::parse($ticket->event->date)->format('d/m/Y H:i') }}',
                    typeName: '{{ addslashes($ticket->ticketType->name ?? 'General') }}',
                    quantity: '{{ $ticket->quantity }}',
                    status: '{{ $ticket->status }}',
                    code: '{{ $ticket->ticket_code }}',
                    qrSvg: `{!! addslashes(SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($ticket->ticket_code)) !!}`
                 }; openModal = true;" 
                 class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between cursor-pointer hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1">
                
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-3 py-1 rounded-full">
                            {{ $ticket->ticketType->name ?? 'General' }}
                        </span>
                        <span class="text-xs font-mono font-bold text-gray-400">
                            {{ $ticket->ticket_code }}
                        </span>
                    </div>

                    <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $ticket->event->title ?? 'Evento GraduFest' }}</h3>
                    <p class="text-xs text-gray-500 mb-4">📍 {{ $ticket->event->location ?? 'Auditorio Principal' }}</p>

                    <div class="bg-gray-50 p-3 rounded-xl text-xs space-y-1 text-gray-600">
                        <p><strong>Cantidad:</strong> {{ $ticket->quantity }} boleto(s)</p>
                        <p><strong>Estatus:</strong> 
                            <span class="font-bold uppercase {{ $ticket->status === 'paid' ? 'text-emerald-600' : 'text-amber-500' }}">
                                {{ $ticket->status === 'paid' ? 'Pagado' : 'Pago Pendiente' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                    <span class="text-xs font-bold text-indigo-600">🔍 Haz clic para ver Código QR</span>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 rounded-2xl text-center text-gray-400 font-medium">
                Aún no tienes boletos apartados. ¡Explora los eventos disponibles!
            </div>
        @endforelse
    </div>

    <!-- VENTANA MODAL (POPUP) PARA VER EL QR Y DETALLES -->
    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition.opacity
         style="display: none;">
        
        <div @click.away="openModal = false" 
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-6 relative transform transition-all">
            
            <!-- Botón Cerrar -->
            <button @click="openModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-xl">
                ✕
            </button>

            <!-- Encabezado Modal -->
            <div class="text-center mb-6">
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase" x-text="selectedTicket.typeName"></span>
                <h3 class="font-extrabold text-xl text-gray-900 mt-2" x-text="selectedTicket.title"></h3>
                <p class="text-xs text-gray-500 mt-1" x-text="'Lugar: ' + selectedTicket.location"></p>
                <p class="text-xs text-indigo-600 font-semibold mt-0.5" x-text="'Fecha: ' + selectedTicket.date"></p>
            </div>

            <!-- Contenedor del Código QR -->
            <div class="flex flex-col items-center justify-center bg-gray-50 p-6 rounded-2xl border border-gray-100">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-3">Folio: <span x-text="selectedTicket.code" class="text-gray-700 font-mono"></span></span>
                
                <!-- Render dinámico del SVG del QR -->
                <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200" x-html="selectedTicket.qrSvg"></div>
                
                <p class="text-[11px] text-gray-400 text-center mt-4">Presenta este código QR en la entrada del evento para validar tu acceso.</p>
            </div>

            <!-- Botón de acción inferior -->
            <div class="mt-6">
                <button @click="openModal = false" class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-gray-800 transition">
                    Cerrar
                </button>
            </div>

        </div>
    </div>

</div>
@endsection