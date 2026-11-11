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
use APP\Http\Controllers\Admin\CheckController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;


// ==========================================
// 1. RUTAS PÚBLICAS Y DE CLIENTES
// ==========================================

Route::get('welcome', function () {
    return view('welcome');
});

Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

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
    
    // 🔴 ELIMINA O CORRIGE ESTA LÍNEA QUE CAUSA EL ERROR:
    // Route::delete('/tickets/{ticket}/cancel', [TicketController::class, 'destroy'])->name('tickets.cancel');
    
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

Route::get('/debug-secure', function (Request $request) {
    return response()->json([
        'is_secure' => $request->secure(),
        'scheme' => $request->getScheme(),
        'url' => $request->url(),
        'header_proto' => $request->header('x-forwarded-proto'),
    ]);
});

// ==========================================
// 2. AUTENTICACIÓN GOOGLE OAUTH
// ==========================================

Route::get('/auth/google', function () {
    return Socialite::driver('google')
        ->redirectUrl(env('GOOGLE_REDIRECT', 'https://ticketsales-i578.onrender.com/auth/google/callback'))
        ->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')
            ->redirectUrl(env('GOOGLE_REDIRECT', 'https://ticketsales-i578.onrender.com/auth/google/callback'))
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

// A. Rutas públicas de autenticación (Login)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });
    
    // Ruta de cierre de sesión (POST)
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});


// B. Rutas protegidas para TODO el personal administrativo (SuAdmin y Staff)
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    
    // Gestión de Eventos
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    // Concentrado y Gestión de Tickets
    Route::get('/tickets-global', [AdminEventController::class, 'ticketReport'])->name('events.tickets.global');
    Route::get('/events/{event}/tickets', [AdminEventController::class, 'ticketReport'])->name('events.tickets');
    Route::get('/tickets/{ticket}', [AdminEventController::class, 'showTicketDetail'])->name('events.tickets.show');
    Route::post('/tickets/{ticket}/mark-as-paid', [AdminEventController::class, 'markTicketAsPaid'])->name('events.tickets.markAsPaid');
    Route::delete('/tickets/{ticket}', [AdminEventController::class, 'destroyTicket'])->name('events.tickets.destroy');

    Route::get('/scanner', [App\Http\Controllers\Admin\CheckController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/process', [App\Http\Controllers\Admin\CheckController::class, 'scanTicket'])->name('scanner.process');
    });


// C. Rutas EXCLUSIVAS para Super Administradores (suadmin)
Route::prefix('admin')->name('admin.')->middleware(['auth:admin', \App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
    Route::resource('admins', App\Http\Controllers\Admin\AdminUserController::class);
    Route::get('/logs', [App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('logs.index');
    
});


// Rutas de autenticación tradicional de Breeze (Login/Registro general de clientes)
require __DIR__.'/auth.php';