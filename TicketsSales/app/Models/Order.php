<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // 👈 Agrega esta propiedad protegida con los campos permitidos:
    protected $fillable = [
        'user_id',
        'event_id',
        'total_amount',
        'status',
        'expires_at',
        'stripe_payment_intent_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id');
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // En tu comando o controlador de expiración (CheckExpiredOrders o similar):
    public function checkExpiration()
{
    // 1. Buscar órdenes pendientes que ya hayan superado su tiempo (expires_at)
    $expiredOrders = Order::where('status', 'pending')
        ->where('expires_at', '<', now())
        ->get();

    foreach ($expiredOrders as $order) {
        // Opción A: Si quieres borrar la orden completa por expiración
        // $order->delete();

        // Opción B: O si prefieres marcar la orden como expirada/cancelada pero borrar los tickets de reserva
        $order->update(['status' => 'expired']);

        // 🎟️ BORRAR FÍSICAMENTE LOS BOLETOS TEMPORALES DE ESA ORDEN
        // Al eliminarlos con delete(), desaparecen de la base de datos y 
        // el stock libre (stock total - registros existentes) sube automáticamente.
        Ticket::where('order_id', $order->id)
              ->where('status', 'reserved')
              ->delete(); 
    }
}
}