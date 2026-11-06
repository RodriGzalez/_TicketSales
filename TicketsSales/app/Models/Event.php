<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Un evento tiene muchos tipos de boletos (Adulto, Niño, etc.)
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class);
}

}