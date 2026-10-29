<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\EventController;

Route::get('welcome', function () {
    return view('welcome');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{id}', [EventController::class, 'show'])->name('events.show');

