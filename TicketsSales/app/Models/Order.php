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
    // Si la orden ya está expirada o su tiempo (expires_at) ya pasó
    if ($this->status === 'pending' && $this->expires_at && now()->greaterThan($this->expires_at)) {
        
        $this->update(['status' => 'expired']);

        // 🎟️ Borrar boletos temporales de esta orden si aplica
        Ticket::where('order_id', $this->id)
              ->where('status', 'reserved')
              ->delete(); 

        return true; // SÍ expiró
    }

    return $this->status === 'expired'; // Ya estaba expirada
}
}