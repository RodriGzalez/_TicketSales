@extends('layouts.admin')

@section('title', 'Editar Administrador - GraduFest')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-black text-gray-900">Editar Administrador</h2>
        <p class="text-xs text-gray-500">Modifica los datos o el nivel de permisos del usuario del panel.</p>
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

    <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre Completo</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nueva Contraseña <span class="text-gray-400 font-normal">(Opcional)</span></label>
                <input type="password" name="password" placeholder="Dejar en blanco para mantener la actual" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Rol de Acceso</label>
                <select name="role" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="suadmin" {{ $admin->role === 'suadmin' ? 'selected' : '' }}>Super Admin (Control Total)</option>
                    <option value="staff" {{ $admin->role === 'staff' ? 'selected' : '' }}>Staff (Gestión Operativa)</option>
                    <option value="checker" {{ $admin->role === 'checker' ? 'selected' : '' }}>Checador de entradas (QR checker)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.admins.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition shadow-md">
                Actualizar Cambios
            </button>
        </div>  
    </form>
</div>
@endsection