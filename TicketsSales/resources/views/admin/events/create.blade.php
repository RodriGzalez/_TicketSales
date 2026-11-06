@extends('layouts.admin')

@section('title', 'Crear Evento - GraduFest')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-black text-gray-900">Crear Nuevo Evento</h2>
        <p class="text-xs text-gray-500">Llena los detalles para dar de alta un evento en la plataforma</p>
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

    <form action="{{ route('admin.events.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Título del Evento</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descripción</label>
            <textarea name="description" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha y Hora</label>
                <input type="datetime-local" name="date" value="{{ old('date') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ubicación / Sede</label>
                <input type="text" name="location" value="{{ old('location') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Capacidad Total</label>
                <input type="number" name="total_capacity" value="{{ old('total_capacity') }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Límite por Usuario</label>
                <input type="number" name="max_tickets_per_user" value="{{ old('max_tickets_per_user', 5) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Expiración Reserva (Min)</label>
                <input type="number" name="reservation_expiry_time" value="{{ old('reservation_expiry_time', 15) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL de la Imagen</label>
                <input type="url" name="image_url" value="{{ old('image_url') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Estatus</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="archived">Archivado</option>
                </select>
            </div>
        </div>
        <div class="space-y-4 border-t border-gray-200 pt-6 mt-6" x-data="{ types: [{name: 'General', price: 0, stock: 100}] }">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Tipos de Boletos para este Evento</h3>
        <button type="button" @click="types.push({name: '', price: 0, stock: 50})" class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-100">
            + Agregar otro tipo de boleto
        </button>
    </div>

    <template x-for="(ticketType, index) in types" :key="index">
        <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre (Ej. General, VIP)</label>
                <input type="text" :name="'ticket_types['+index+'][name]'" x-model="ticketType.name" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Precio ($)</label>
                <input type="number" step="0.01" :name="'ticket_types['+index+'][price]'" x-model="ticketType.price" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Stock / Cantidad</label>
                <input type="number" :name="'ticket_types['+index+'][stock]'" x-model="ticketType.stock" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <button type="button" @click="types.splice(index, 1)" x-show="types.length > 1" class="text-red-500 hover:text-red-700 p-2">
                🗑️
            </button>
        </div>
    </template>
</div>
        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition shadow-md">
                Guardar Evento
            </button>
        </div>
    </form>
</div>
@endsection