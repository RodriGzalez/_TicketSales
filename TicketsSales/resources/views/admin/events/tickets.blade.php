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
        
                    <!-- 🌐 Opción Global -->
                    <option value="{{ route('admin.events.tickets.global') }}" {{ !isset($event) || !$event ? 'selected' : '' }}>
                        ✨ Todos los Eventos (Global)
                    </option>

                    <!-- 🎟️ Opciones Individuales -->
                    @foreach($events as $ev)
                        <option value="{{ route('admin.events.tickets', $ev) }}" {{ isset($event) && $event && $event->id === $ev->id ? 'selected' : '' }}>
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
        @if(isset($event) && $event)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-4">Control de Stock por Tipo de Boleto</h3>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[700px]">
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
                                // 1. Reservados (Órdenes pendientes con vigencia activa)
                                $reservedQty = \App\Models\OrderItem::where('ticket_type_id', $type->id)
                                    ->whereHas('order', function($q) use ($event) {
                                        $q->where('event_id', $event->id)
                                        ->where('status', 'pending')
                                        ->where('expires_at', '>', now());
                                    })->sum('quantity');

                                // 2. Pagados (Órdenes con estatus 'paid')
                                $paidQty = \App\Models\OrderItem::where('ticket_type_id', $type->id)
                                    ->whereHas('order', function($q) use ($event) {
                                        $q->where('event_id', $event->id)
                                        ->where('status', 'paid');
                                    })->sum('quantity');

                                // 3. Liquidados (Órdenes con estatus 'liquidate')
                                $liquidateQty = \App\Models\OrderItem::where('ticket_type_id', $type->id)
                                    ->whereHas('order', function($q) use ($event) {
                                        $q->where('event_id', $event->id)
                                        ->where('status', 'liquidate');
                                    })->sum('quantity');

                                // 4. Pago pendiente (Si aplica en tu flujo)
                                $pendingPaymentQty = \App\Models\OrderItem::where('ticket_type_id', $type->id)
                                    ->whereHas('order', function($q) use ($event) {
                                        $q->where('event_id', $event->id)
                                        ->where('status', 'paid_pending');
                                    })->sum('quantity');

                                // Total ocupados y cálculo de stock disponible real
                                $ocupados = $reservedQty + $pendingPaymentQty + $paidQty + $liquidateQty;
                                $disponiblesReal = $type->stock - $ocupados;
                            @endphp
                            
                            <tr class="hover:bg-gray-50/50 transition align-middle">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $type->name }}</td>
                                <td class="py-4 px-6 text-center font-semibold text-emerald-600">${{ number_format($type->price, 2) }}</td>
                                <td class="py-4 px-6 text-center font-bold text-gray-800">{{ $type->stock }}</td>
                                
                                <!-- Reservados -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-block bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">{{ $reservedQty }}</span>
                                </td>

                                <!-- Pago Pendiente -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">{{ $pendingPaymentQty }}</span>
                                </td>

                                <!-- Pagados -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-block bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-1 rounded-full">{{ $paidQty }}</span>
                                </td>

                                <!-- Liquidados -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-block bg-purple-50 text-purple-600 text-xs font-bold px-3 py-1 rounded-full">{{ $liquidateQty }}</span>
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
        @endif

        <!-- 🧾 TABLA 2: LISTADO DE TRANSACCIONES Y FOLIOS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
            
            <!-- 🔍 FILTROS Y BUSCADOR -->
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-lg text-gray-950">Listado de Transacciones y Folios</h3>

                <form method="GET" action="{{ isset($event) && $event ? route('admin.events.tickets', $event) : url()->current() }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    
                    <!-- Buscador por texto -->
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar folio, nombre o correo..." 
                           class="text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 w-full md:w-64">

                    <!-- Filtro por Estatus -->
                    <select name="status" class="text-xs border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Todos los estatus</option>
                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reservado</option>
                        <option value="paid_pending" {{ request('status') == 'paid_pending' ? 'selected' : '' }}>Pago Pendiente</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="liquidate" {{ request('status') == 'liquidate' ? 'selected' : '' }}>Liquidado</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    
                    <!-- Filtro por Método de Pago -->
                    <select name="payment_method" class="text-xs border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Método de Pago</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Pago contra entrega</option>
                    </select>

                    <!-- Botones -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'status', 'payment_method']))
                        <a href="{{ isset($event) && $event ? route('admin.events.tickets', $event) : url()->current() }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-2">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- TABLA DE TRANSACCIONES -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-4 px-6">Folio</th>
                            <th class="py-4 px-6">Nombre de Usuario</th>
                            <th class="py-4 px-6">Tipo / Cantidad</th>
                            <th class="py-4 px-6 text-center">Método de Pago</th>
                            <th class="py-4 px-6 text-right">Costo Total</th>
                            <th class="py-4 px-6 text-center">Fecha de Compra</th>
                            <th class="py-4 px-6 text-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($tickets as $ticket)
                            @php
                                $quantity = $ticket->quantity ?? 1;
                                $subtotal = $quantity * ($ticket->ticketType->price ?? 0);
                                $method = $ticket->payment_method ?? 'not_specified';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition align-middle">
                                
                                <!-- FOLIO -->
                                <td class="py-4 px-6 font-mono font-bold text-indigo-600 whitespace-nowrap">
                                    <a href="{{ route('admin.events.tickets.show', $ticket) }}" class="hover:underline hover:text-indigo-800 flex items-center gap-1">
                                        {{ $ticket->ticket_code }} &rarr;
                                    </a>
                                </td>

                                <!-- NOMBRE DE USUARIO -->
                                <td class="py-4 px-6 font-medium text-gray-900">
                                    <div class="leading-snug">{{ $ticket->user->name ?? 'Usuario desconocido' }}</div>
                                    <span class="block text-xs text-gray-400 mt-0.5">{{ $ticket->user->email ?? '' }}</span>
                                </td>

                                <!-- TIPO / CANTIDAD -->
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-gray-800 leading-snug block">{{ $ticket->ticketType->name ?? 'General' }}</span>
                                    <span class="block text-xs text-indigo-600 font-bold mt-0.5">Cant: {{ $quantity }}</span>
                                </td>

                                <!-- MÉTODO DE PAGO -->
                                <td class="py-4 px-6 whitespace-nowrap text-center">
                                    @if($method === 'cash_on_delivery' || $method === 'cash')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                            Pago contra entrega
                                        </span>
                                    @elseif($method === 'card' || $method === 'stripe')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">
                                            Pago con Tarjeta
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500">
                                            No especificado
                                        </span>
                                    @endif
                                </td>

                                <!-- COSTO TOTAL -->
                                <td class="py-4 px-6 text-right font-bold text-emerald-600 whitespace-nowrap">
                                    ${{ number_format(optional($ticket->order)->total_amount ?? ($ticket->quantity * $ticket->price), 2) }}
                                </td>

                                <!-- FECHA DE COMPRA -->
                                <td class="py-4 px-6 text-center text-gray-500 text-xs whitespace-nowrap">
                                    {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>

                                <!-- ESTATUS -->
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if($ticket->status === 'reserved')
                                        <span class="inline-block bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">Reservado</span>
                                    @elseif($ticket->status === 'paid_pending')
                                        <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full">Pendiente de pago</span>
                                    @elseif($ticket->status === 'paid')
                                        <span class="inline-block bg-emerald-50 text-emerald-600 text-xs font-bold px-3 py-1 rounded-full">Pagado</span>
                                    @elseif($ticket->status === 'liquidate')
                                        <span class="inline-block bg-purple-50 text-purple-600 text-xs font-bold px-3 py-1 rounded-full">Liquidado</span>
                                    @elseif($ticket->status === 'cancelled')
                                        @if(str_starts_with($ticket->ticket_code, 'TICK-'))
                                            <span class="inline-block bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full">Cancelado (Timeout)</span>
                                        @else
                                            <span class="inline-block bg-rose-50 text-rose-700 text-xs font-bold px-3 py-1 rounded-full">Cancelado</span>
                                        @endif
                                    @else
                                        <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">{{ ucfirst($ticket->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400 font-medium">
                                    No se encontraron transacciones con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $tickets->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection