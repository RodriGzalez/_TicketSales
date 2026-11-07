<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\EventController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Client\TicketController as ClientTicketController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\OrderController;

// ==========================================
// 1. RUTAS PÚBLICAS Y DE CLIENTES
// ==========================================

Route::get('welcome', function () {
    return view('welcome');
});

/// 1️⃣ RUTAS FIJAS O ESPECÍFICAS (Siempre van primero)
Route::get('/checkout/success', [CheckoutController::class, 'paymentSuccess'])->name('checkout.success'); 

// 2️⃣ RUTAS DINÁMICAS QUE TIENEN {order}
// Muestra la vista donde el usuario elige entre Tarjeta o Contra Entrega (DEBE SER GET)
Route::get('/checkout/{order}/select', [CheckoutController::class, 'showSelection'])->name('checkout.select');

// Procesa el formulario cuando el usuario selecciona el método de pago (DEBE SER POST)
Route::post('/checkout/{order}/process', [CheckoutController::class, 'processSelection'])->name('checkout.process-selection');

// Muestra la pasarela de pago directa con Stripe (DEBE SER GET)
Route::get('/checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{id}', [EventController::class, 'show'])->name('events.show');

// Compra / Apartado de boletos (Requiere sesión de cliente + Throttle anti-spam de 10 peticiones por minuto)
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/events/{event}/tickets', [ClientTicketController::class, 'store'])->name('tickets.store');
    
    // Perfil del cliente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/tickets/{ticket}', [ClientTicketController::class, 'destroy'])->name('tickets.destroy');
    Route::get('/mis-boletos', [ClientEventController::class, 'myTickets'])->name('client.tickets');

    Route::post('/events/{event}/order', [OrderController::class, 'store'])->name('orders.store');
    });
   

Route::prefix('admin')->name('admin.payments.')->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('show');
});

// Panel del cliente (Breeze)
Route::get('/dashboard', function () {
    // Obtenemos los boletos del usuario actual junto con su evento relacionado
    $tickets = Auth::user()->tickets()->with('event')->latest()->get();
    
    return view('dashboard', compact('tickets'));
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// 2. AUTENTICACIÓN GOOGLE OAUTH
// ==========================================

Route::get('/auth/google', function () {
    return Socialite::driver('google')
        ->redirectUrl(env('GOOGLE_REDIRECT', 'http://localhost:8000/auth/google/callback'))
        ->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')
            ->redirectUrl(env('GOOGLE_REDIRECT', 'http://localhost:8000/auth/google/callback'))
            ->stateless()
            ->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => Hash::make(Str::random(24)),
            ]
        );

        Auth::login($user);

        return redirect()->route('events.index');
        
    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Error en la autenticación con Google: ' . $e->getMessage());
    }
});


// ==========================================
// 3. PANEL DE ADMINISTRACIÓN Y LOGIN ADMIN
// ==========================================

Route::prefix('admin')->name('admin.')->group(function () {
    // Rutas públicas de autenticación de admin (Protegidas con Throttle estricto: 5 intentos por minuto contra fuerza bruta)
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Rutas protegidas exclusivamente por el guardia de administradores
    // Route::middleware(['auth:admin'])->group(function () {
        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
        Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
        
        // Reportes globales y de eventos (Ubicados correctamente dentro del panel admin)
        Route::get('/tickets-report', [AdminEventController::class, 'globalTicketReport'])->name('tickets.global');
        Route::get('/concentrado-boletos', [AdminEventController::class, 'globalTicketReport'])->name('concentrado');
        Route::get('/events/{event?}/tickets', [AdminEventController::class, 'ticketReport'])->name('events.tickets');

        // Reporte de boletos (redirige al primer evento por defecto o muestra el seleccionado)
        Route::get('/admin/events/tickets', [App\Http\Controllers\Admin\EventController::class, 'globalTicketReport'])->name('admin.events.tickets.global');

// Reporte de boletos filtrado por un evento específico
        Route::get('/admin/events/{event}/tickets', [App\Http\Controllers\Admin\EventController::class, 'ticketReport'])->name('admin.events.tickets');
    });
// });

// Rutas de autenticación tradicional de Breeze (Login/Registro general de clientes)
require __DIR__.'/auth.php';