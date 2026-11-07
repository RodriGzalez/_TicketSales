<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getAvailableTicketsAttribute()
{
    $occupiedCount = $this->tickets()
                          ->whereIn('status', ['reserved', 'sold', 'paid']) // 👈 'reserved' cuenta como ocupado
                          ->count();

    return $this->total_stock - $occupiedCount;
}
}