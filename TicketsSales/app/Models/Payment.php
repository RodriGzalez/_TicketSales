<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Obtiene la orden asociada al pago.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}