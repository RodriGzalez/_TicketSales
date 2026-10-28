<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\EventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');

