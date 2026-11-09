@extends('layouts.admin')

@section('title', 'Nuevo Administrador - GraduFest')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-black text-gray-900">Crear Nuevo Administrador</h2>
        <p class="text-xs text-gray-500">Registra un nuevo usuario para el panel de control y asígnale sus permisos.</p>
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

    <form action="{{ route('admin.admins.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre Completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Rol de Acceso</label>
                <select name="role" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="suadmin">Super Admin (Acceso Total)</option>
                    <option value="staff">Staff (Gestión Operativa)</option>
                    <option value="checker">Checador de entradas (QR checker)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.admins.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition shadow-md">
                Guardar Usuario
            </button>
        </div>
    </form>
</div>
@endsection