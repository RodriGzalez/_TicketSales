<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_type_id', // <--- Asegúrate de incluir esta línea
        'quantity',
        'ticket_code',
        'status',
        'expires_at',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}