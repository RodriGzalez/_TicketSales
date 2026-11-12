<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;


class CheckController extends Controller
{
    public function index()
    {
        return view('admin.scanner.index');
    }

public function scanTicket(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim($request->code);

        // Buscamos el boleto por 'ticket_code'
        $ticket = Ticket::where('ticket_code', $code)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ El boleto o folio ingresado no existe en el sistema.'
            ], 404);
        }

        // Validar si ya fue utilizado
        if ($ticket->status === 'liquidate' || $ticket->status === 'used') {
            return response()->json([
                'status' => 'warning',
                'message' => '⚠️ ALERTA: Este boleto YA FUE ESCANEADO Y UTILIZADO anteriormente.'
            ], 400);
        }

        if ($ticket->status === 'cancelled' || $ticket->status === 'paid_pending') {
            return response()->json([
                'status' => 'error',
                'message' => '❌ El boleto no es válido para acceso (Estatus: ' . $ticket->status . ').'
            ], 400);
        }

        // Marcamos como liquidado/usado
        $ticket->status = 'liquidate';
        $ticket->save();

        // Obtenemos el usuario autenticado del panel administrativo que realiza el escaneo
        $adminUser = auth('admin')->user();

        return response()->json([
            'status' => 'success',
            'message' => '✅ ¡Acceso concedido! Boleto validado correctamente.',
            'ticket' => [
                'folio' => $ticket->ticket_code ?? 'N/A',
                'name' => $ticket->order->user->name ?? 'Estudiante',
                'event_description' => $ticket->orderItem->ticketType->event->description ?? $ticket->orderItem->ticketType->event->title ?? 'Evento Graduación',
                'quantity_and_type' => ($ticket->quantity ?? 1) . 'x ' . ($ticket->orderItem->ticketType->name ?? 'General'),
                'staff_name' => $adminUser ? $adminUser->name : 'Personal Autorizado'
            ]
        ]);
    }
}
