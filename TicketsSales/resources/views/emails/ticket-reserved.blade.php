<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f7; color: #333; padding: 20px; }
        .card { background: #ffffff; padding: 30px; border-radius: 12px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        .folio { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .qr-container { text-align: center; margin: 20px 0; }
        .details { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>🎓 GraduFest - Confirmación de Reserva</h2>
        </div>

        <p>¡Hola, <strong>{{ $ticket->user->name }}</strong>!</p>
        <p>Tu orden de boletos ha sido apartada con éxito. Presenta el siguiente código QR en la entrada del evento.</p>

        <div class="details">
            <p><strong>Evento:</strong> {{ $ticket->event->title }}</p>
            <p><strong>Lugar:</strong> {{ $ticket->event->location ?? 'Auditorio' }}</p>
            <p><strong>Fecha:</strong> {{ $ticket->event->date ?? 'Por definir' }}</p>
            <p><strong>Tipo de Boleto:</strong> {{ $ticket->ticketType->name }} (Cant: {{ $ticket->quantity }})</p>
            <p>Folio de Acceso: <span class="folio">{{ $ticket->ticket_code }}</span></p>
        </div>

        <div class="qr-container">
            <p style="font-size: 12px; color: #666; margin-bottom: 5px;">Código QR de acceso:</p>
            <!-- Renderizamos el SVG del QR -->
            {!! $qrCodeSvg !!}
        </div>

        <p style="font-size: 12px; color: #999; text-align: center; margin-top: 30px;">
            Si tienes dudas, contacta al comité organizador de GraduFest.
        </p>
    </div>
</body>
</html>