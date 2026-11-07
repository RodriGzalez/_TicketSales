<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $expiredOrders = Order::where('status', 'pending')
        ->where('expires_at', '<', now())
        ->get();

    foreach ($expiredOrders as $order) {
        $order->update(['status' => 'cancelled']);
        \App\Models\Ticket::where('order_id', $order->id)->update(['status' => 'cancelled']);
    }
})->everyMinute();