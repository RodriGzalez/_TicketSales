@extends('layouts.admin')

@section('title', 'Gestión de Administradores - GraduFest')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-black text-gray-900">Usuarios del Panel Admin</h2>
            <p class="text-xs text-gray-500">Administra las cuentas y roles del personal con acceso al sistema.</p>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition shadow-md">
            + Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-600 text-sm rounded-xl border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold border-b border-gray-100">
                    <th class="p-4">Nombre</th>
                    <th class="p-4">Correo</th>
                    <th class="p-4">Rol</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($admins as $adm)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-gray-800">{{ $adm->name }}</td>
                        <td class="p-4 text-gray-600">{{ $adm->email }}</td>
                        <td class="p-4">
                            @php
                                $badgeColors = [
                                    'suadmin' => 'bg-purple-50 text-purple-600',
                                    'checker' => 'bg-emerald-50 text-emerald-600',
                                    'staff'   => 'bg-blue-50 text-blue-600',
                                ];
                                
                                $roleLabels = [
                                    'suadmin' => 'Super Admin',
                                    'checker' => 'Checker',
                                    'staff'   => 'Staff',
                                ];

                                $currentRole = $adm->role ?? 'staff';
                            @endphp

                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeColors[$currentRole] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $roleLabels[$currentRole] ?? ucfirst($currentRole) }}
                            </span>
                        </td>
                        <td class="p-4 text-right flex items-center justify-end gap-3">
                            <!-- Botón de Editar -->
                            <a href="{{ route('admin.admins.edit', $adm) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs">
                                Editar
                            </a>

                            @if($adm->id !== auth('admin')->id())
                                <form action="{{ route('admin.admins.destroy', $adm) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                        Eliminar
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">(Tu usuario)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection