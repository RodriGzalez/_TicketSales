@extends('layouts.admin')

@section('title', 'Gestión de Eventos - GraduFest')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-900">Eventos</h2>
            <p class="text-xs text-gray-500">Administra los eventos de graduación y su estatus</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition shadow-md">
            + Nuevo Evento
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-100 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider font-bold border-b border-gray-100">
                    <th class="py-4 px-6">Evento</th>
                    <th class="py-4 px-6">Fecha</th>
                    <th class="py-4 px-6">Capacidad</th>
                    <th class="py-4 px-6">Estatus</th>
                    <th class="py-4 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 px-6 font-semibold text-gray-900">{{ $event->title }}</td>
                        <td class="py-4 px-6">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}</td>
                        <td class="py-4 px-6">{{ $event->total_capacity }} boletos</td>
                        <td class="py-4 px-6">
                            @if($event->status === 'active')
                                <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-600 rounded-full">Activo</span>
                            @elseif($event->status === 'inactive')
                                <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-600 rounded-full">Inactivo</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded-full">Archivado</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Editar</a>
                            
                            <!-- Botón de Archivado Lógico -->
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de archivar este evento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-amber-600 hover:text-amber-900 font-medium">Archivar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400 text-sm">No hay eventos registrados todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection