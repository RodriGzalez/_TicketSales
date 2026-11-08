<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_type_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🎟️ Agregamos esta relación que faltaba
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}