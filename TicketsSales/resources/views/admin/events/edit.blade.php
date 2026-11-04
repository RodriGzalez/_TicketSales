@extends('layouts.admin')

@section('title', 'Editar Evento - GraduFest')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-black text-gray-900">Editar Evento</h2>
        <p class="text-xs text-gray-500">Actualiza los datos del evento "{{ $event->title }}"</p>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.events.update', $event) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Título del Evento</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descripción</label>
            <textarea name="description" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha y Hora</label>
                <input type="datetime-local" name="date" value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ubicación / Sede</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Capacidad Total</label>
                <input type="number" name="total_capacity" value="{{ old('total_capacity', $event->total_capacity) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Límite por Usuario</label>
                <input type="number" name="max_tickets_per_user" value="{{ old('max_tickets_per_user', $event->max_tickets_per_user) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Expiración Reserva (Min)</label>
                <input type="number" name="reservation_expiry_time" value="{{ old('reservation_expiry_time', $event->reservation_expiry_time) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL de la Imagen</label>
                <input type="url" name="image_url" value="{{ old('image_url', $event->image_url) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Estatus</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ $event->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    <option value="archived" {{ $event->status === 'archived' ? 'selected' : '' }}>Archivado</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition shadow-md">
                Actualizar Evento
            </button>
        </div>
    </form>
</div>
@endsection