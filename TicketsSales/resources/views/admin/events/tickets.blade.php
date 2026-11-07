@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- ENCABEZADO Y SELECTOR DE EVENTOS -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    🎟️ Concentrado de Boletaje y Stock
                </h2>
                <p class="text-xs text-gray-500 mt-1">Filtra y revisa el inventario, folios y costos por evento</p>
            </div>

            <!-- Selector desplegable -->
            <div class="flex items-center gap-3 bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-200 shadow-inner">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Cambiar Evento:</span>
                <select onchange="window.location.href=this.value" class="border-none text-sm font-semibold text-indigo-600 focus:ring-0 cursor-pointer bg-transparent">
                    @foreach($events as $ev)
                        <option value="{{ route('admin.events.tickets', $ev) }}" {{ $event && $event->id === $ev->id ? 'selected' : '' }}>
                            {{ $ev->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tarjetas de Resumen Estadístico -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Activos / Válidos</p>
                <p class="text-3xl font-black text-indigo-600 mt-1">{{ $stats['total_sold'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Reservados</p>
                <p class="text-3xl font-black text-amber-500 mt-1">{{ $stats['reserved'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pagados</p>
                <p class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['paid'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cancelados</p>
                <p class="text-3xl font-black text-red-500 mt-1">{{ $stats['cancelled'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 bg-indigo-50/30 border-indigo-100">
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Costo Total (Ingresos)</p>
                <p class="text-2xl font-black text-indigo-700 mt-1">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- 📊 TABLA 1: CONTROL DE STOCK POR TIPO DE BOLETO -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-4">Control de Stock por Tipo de Boleto</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3 px-6">Tipo de Boleto</th>
                            <th class="py-3 px-6 text-center">Precio</th>
                            <th class="py-3 px-6 text-center">Total Stock Inicial</th>
                            <th class="py-3 px-6 text-center">Reservados</th>
                            <th class="py-3 px-6 text-center">Pago Pendiente</th>
                            <th class="py-3 px-6 text-center">Pagados</th>
                            <th class="py-3 px-6 text-center">Liquidados</th>
                            <th class="py-3 px-6 text-center">Disponibles Real</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @foreach($event->ticketTypes as $type)
                            @php
                                $ocupados = ($type->reserved_count ?? 0) 
                                          + ($type->paid_pending_count ?? 0) 
                                          + ($type->paid_count ?? 0) 
                                          + ($type->liquidate_count ?? 0);
                                          
                                $disponiblesReal = $type->stock - $ocupados;
                            @endphp
                            
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $type->name }}</td>
                                <td class="py-4 px-6 text-center font-semibold text-emerald-600">${{ number_format($type->price, 2) }}</td>
                                <td class="py-4 px-6 text-center font-bold text-gray-800">{{ $type->stock }}</td>
                                
                                <!-- Reservados (Proceso en línea) -->
                                <td class="py-4 px-6 text-center">
                                    <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">{{ $type->reserved_count ?? 0 }}</span>
                                </td>

                                <!-- Pago Contra Entrega (paid_pending) -->
                                <td class="py-4 px-6 text-center">
                                    <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">{{ $type->paid_pending_count ?? 0 }}</span>
                                </td>

                                <!-- Pagados -->
                                <td class="py-4 px-6 text-center">
                                    <span class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-1 rounded-full">{{ $type->paid_count ?? 0 }}</span>
                                </td>

                                <!-- Liquidados / Escaneados -->
                                <td class="py-4 px-6 text-center">
                                    <span class="bg-purple-50 text-purple-600 text-xs font-bold px-3 py-1 rounded-full">{{ $type->liquidate_count ?? 0 }}</span>
                                </td>

                                <!-- Disponibles Real -->
                                <td class="py-4 px-6 text-center font-black text-indigo-600">
                                    {{ max(0, $disponiblesReal) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 🧾 TABLA 2: LISTADO DE TRANSACCIONES Y FOLIOS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-lg text-gray-900">Listado de Transacciones y Folios</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-4 px-6">Folio</th>
                    <th class="py-4 px-6">Nombre de Usuario</th>
                    <th class="py-4 px-6">Tipo / Cantidad</th>
                    <th class="py-4 px-6">Costo Total</th>
                    <th class="py-4 px-6">Fecha de Compra</th>
                    <th class="py-4 px-6">Estatus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse($tickets as $ticket)
                    @php
                        $quantity = $ticket->quantity ?? 1;
                        $price = $ticket->ticketType->price ?? 0;
                        $subtotal = $quantity * $price;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 px-6 font-mono font-bold text-indigo-600">{{ $ticket->ticket_code }}</td>
                        <td class="py-4 px-6 font-medium text-gray-900">
                            {{ $ticket->user->name ?? 'Usuario desconocido' }}
                            <span class="block text-xs text-gray-400">{{ $ticket->user->email ?? '' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-semibold text-gray-800">{{ $ticket->ticketType->name ?? 'General' }}</span>
                            <span class="block text-xs text-indigo-600 font-bold">Cant: {{ $quantity }}</span>
                        </td>
                        <td class="py-4 px-6 font-bold text-emerald-600">
                            ${{ number_format($subtotal, 2) }}
                        </td>
                        <td class="py-4 px-6 text-gray-500 text-xs">
                            {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{-- 🛑 REGLA EN ADMIN: Mostrar estatus real sin ocultar nada --}}
                            @if(str_starts_with($ticket->ticket_code, 'TICK') && $ticket->status === 'reserved')
                                <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full">Cancelado (Expirado)</span>
                            @elseif($ticket->status === 'reserved')
                                <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">Reservado</span>
                            @elseif($ticket->status === 'paid_pending')
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">Pago Pendiente</span>
                            @elseif($ticket->status === 'paid')
                                <span class="bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-1 rounded-full">Pagado</span>
                            @elseif($ticket->status === 'liquidate')
                                <span class="bg-purple-50 text-purple-600 text-xs font-bold px-3 py-1 rounded-full">Liquidado</span>
                            @else
                                <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full">Cancelado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400 font-medium">
                            No hay boletos registrados para este evento todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
            @if(method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection