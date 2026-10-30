@extends('layouts.app')

@section('title', 'Crear Cuenta - GraduFest')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 px-4">
    
    <!-- Contenedor principal estilo tarjeta -->
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
        
        <!-- Encabezado de la Tarjeta -->
        <div class="text-center mb-6">
            <span class="text-3xl">🎓</span>
            <h2 class="text-2xl font-black text-gray-900 mt-2">Crea tu cuenta</h2>
            <p class="text-sm text-gray-500 mt-1">Regístrate para apartar tus boletos de graduación</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block font-medium text-sm text-gray-700">Nombre Completo</label>
                <input id="name" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block font-medium text-sm text-gray-700">Correo Electrónico</label>
                <input id="email" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block font-medium text-sm text-gray-700">Contraseña</label>
                <input id="password" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Contraseña</label>
                <input id="password_confirmation" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Botón de Registro -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-indigo-700 transition duration-200 shadow-md text-sm">
                    Crear Cuenta
                </button>
            </div>

            <!-- Enlace a Login -->
            <div class="text-center pt-4 border-t border-gray-100 mt-6">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection