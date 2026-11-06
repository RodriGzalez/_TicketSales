<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketReservedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $qrCodeSvg;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        
        // Generamos el código QR basado en el código único del boleto (folio)
        $this->qrCodeSvg = QrCode::size(200)->generate($ticket->ticket_code);
    }

    public function build()
    {
        return $this->subject('🎟️ ¡Tus boletos para el GraduFest han sido apartados!')
                    ->view('emails.ticket-reserved');
    }
}