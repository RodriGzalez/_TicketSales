@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bitácora de Actividad</h1>
            <p class="text-sm text-gray-500">Historial completo de acciones realizadas por los administradores en el sistema.</p>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
        {{-- 👈 Agregamos w-full aquí --}}
        <table class="w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Fecha / Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Administrador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Módulo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Acción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/6">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-auto">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $log->admin->name ?? 'Sistema / Eliminado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->action === 'CREATED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">CREADO</span>
                            @elseif($log->action === 'UPDATED')
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">ACTUALIZADO</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">ELIMINADO</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                            {{ $log->ip_address ?? 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay registros en la bitácora todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>
@endsection