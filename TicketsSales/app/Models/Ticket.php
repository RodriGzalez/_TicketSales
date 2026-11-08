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
        'order_id',
        'ticket_code',
        'payment_method',
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
public function order()
{
    return $this->belongsTo(Order::class, 'order_id');
}

protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            // Obtenemos el evento asociado
            $event = Event::find($ticket->event_id);
            
            if ($event && $event->max_tickets_per_user > 0) {
                // Sumamos cuántos boletos ya tiene este usuario para este evento
                $existingCount = self::where('event_id', $event->id)
                    ->where('user_id', $ticket->user_id)
                    ->where('status', '!=', 'cancelled')
                    ->sum('quantity');

                $newTotal = $existingCount + $ticket->quantity;

                // Si la suma rebasa el límite del evento, lanzamos una excepción o bloqueamos
                if ($newTotal > $event->max_tickets_per_user) {
                    throw new Exception("Límite excedido: El usuario ya tiene {$existingCount} boletos y el máximo permitido por el evento es {$event->max_tickets_per_user}.");
                }
            }
        });
    }
}