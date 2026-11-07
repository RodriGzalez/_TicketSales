<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conciliación Financiera - Admin GraduFest</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-black text-gray-900 mb-2">Conciliación Financiera</h1>
        <p class="text-xs text-gray-500 mb-6">Monitoreo de ingresos, pasarelas de pago y auditoría de transacciones.</p>

        <!-- Tarjetas de Métricas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <p class="text-xs font-bold text-gray-400 uppercase">Total Cobrado (Pagado)</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">${{ number_format($totalCollected, 2) }} MXN</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <p class="text-xs font-bold text-gray-400 uppercase">Pagos Pendientes</p>
                <p class="text-2xl font-black text-amber-600 mt-1">${{ number_format($totalPending, 2) }} MXN</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <p class="text-xs font-bold text-gray-400 uppercase">Devoluciones / Refunds</p>
                <p class="text-2xl font-black text-red-600 mt-1">${{ number_format($totalRefunded, 2) }} MXN</p>
            </div>
        </div>

        <!-- Tabla de Transacciones -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-sm">Historial de Transacciones</h3>
            </div>
            
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase border-b border-gray-200">
                        <th class="p-4">ID Transacción</th>
                        <th class="p-4">Orden</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Monto</th>
                        <th class="p-4">Gateway</th>
                        <th class="p-4">Estatus</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-mono text-gray-600">{{ $payment->gateway_transaction_id ?? 'N/A' }}</td>
                            <td class="p-4 font-bold">#{{ $payment->order_id }}</td>
                            <td class="p-4">{{ $payment->order->user->name ?? 'Usuario desconocido' }}</td>
                            <td class="p-4 font-bold">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="p-4 uppercase font-semibold text-gray-600">{{ $payment->gateway }}</td>
                            <td class="p-4">
                                @if($payment->status === 'paid')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full font-bold">Pagado</span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full font-bold">Pendiente</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full font-bold">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-indigo-600 font-bold hover:underline">Auditar JSON</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-400">No hay pagos registrados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

</body>
</html>