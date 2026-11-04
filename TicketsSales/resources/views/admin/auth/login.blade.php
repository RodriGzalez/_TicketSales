@extends('layouts.admin')

@section('title', 'Acceso Administrativo - GraduFest')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <div class="text-center mb-6">
            <span class="text-3xl">🔐</span>
            <h2 class="text-2xl font-black text-gray-900 mt-2">Panel de Administración</h2>
            <p class="text-xs text-gray-500 mt-1">Ingresa con tus credenciales de gestor</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs rounded-xl border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-semibold py-3.5 rounded-xl transition shadow-lg text-sm">
                Entrar al Panel
            </button>
        </form>
    </div>
</div>
@endsection