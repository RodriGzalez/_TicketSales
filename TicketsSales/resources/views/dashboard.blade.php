<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-100 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mis Boletos Apartados</h3>

                <div class="space-y-4">
                    @forelse($tickets as $ticket)
                        <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
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
                            
                            <div class="flex items-center gap-4">
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

                                <!-- Botón de Cancelar / Eliminar Reserva -->
                                @if($ticket->status === 'reserved')
                                    <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar este apartado? Se liberarán tus boletos.');">
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
    </div>
</x-app-layout>