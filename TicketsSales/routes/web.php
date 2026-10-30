<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\EventController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::get('welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{id}', [EventController::class, 'show'])->name('events.show');

// Ruta para redirigir al usuario a Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')
        ->redirectUrl(env('GOOGLE_REDIRECT', 'http://localhost:8000/auth/google/callback'))
        ->redirect();
})->name('google.login');

// Ruta que recibe la respuesta de Google (Callback)
Route::get('/auth/google/callback', function () {
    try {
        // Forzamos también el redirectUrl aquí para que coincida en el intercambio de token
        $googleUser = Socialite::driver('google')
            ->redirectUrl(env('GOOGLE_REDIRECT', 'http://localhost:8000/auth/google/callback'))
            ->user();

        // Buscar si ya existe el usuario o crearlo
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => Hash::make(Str::random(24)),
            ]
        );

        // Iniciar sesión
        Auth::login($user);

        // Redirigir al inicio / catálogo
        return redirect()->route('events.index');
        
    } catch (\Exception $e) {
        // Si vuelve a fallar, veremos el mensaje exacto aquí
        dd($e->getMessage());
    }
});

require __DIR__.'/auth.php';
