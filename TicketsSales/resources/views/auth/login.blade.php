@extends('layouts.app')

@section('title', 'Iniciar Sesión - GraduFest')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 px-4">
    
    <!-- Contenedor principal estilo tarjeta -->
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
        
        <!-- Encabezado de la Tarjeta -->
        <div class="text-center mb-6">
            <span class="text-3xl">🎓</span>
            <h2 class="text-2xl font-black text-gray-900 mt-2">Bienvenido de nuevo</h2>
            <p class="text-sm text-gray-500 mt-1">Inicia sesión para gestionar tus boletos y eventos</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block font-medium text-sm text-gray-700">Correo Electrónico</label>
                <input id="email" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block font-medium text-sm text-gray-700">Contraseña</label>
                <input id="password" class="block mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-gray-600">Recuérdame</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-indigo-600 hover:text-indigo-900 font-medium" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Botón de Iniciar Sesión -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-indigo-700 transition duration-200 shadow-md text-sm">
                    Iniciar Sesión
                </button>
            </div>
            
            <div>
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-50 transition duration-200 shadow-sm text-sm">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google logo">
                Continuar con Google
            </a>
            </div>

            <!-- Enlace a Registro -->
            <div class="text-center pt-4 border-t border-gray-100 mt-6">
                <p class="text-sm text-gray-600">
                    ¿Aún no tienes cuenta? 
                    <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Regístrate aquí</a>
                </p>
            </div>


        </form>
    </div>
</div>
@endsection