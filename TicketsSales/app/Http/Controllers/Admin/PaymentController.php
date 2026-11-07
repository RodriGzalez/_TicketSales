<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Muestra el panel de conciliación financiera para el administrador.
     */
    public function index(Request $request)
    {
        // 1. Obtener filtros opcionales (por estatus o pasarela)
        $status = $request->input('status');
        $gateway = $request->input('gateway');

        $query = Payment::with('order.user', 'order.event');

        if ($status) {
            $query->where('status', $status);
        }

        if ($gateway) {
            $query->where('gateway', $gateway);
        }

        $payments = $query->latest()->paginate(15);

        // 2. Calcular métricas clave para la contabilidad
        $totalCollected = Payment::where('status', 'paid')->sum('amount');
        $totalPending = Payment::where('status', 'pending')->sum('amount');
        $totalRefunded = Payment::where('status', 'refunded')->sum('amount');

        return view('admin.payments.index', compact(
            'payments',
            'totalCollected',
            'totalPending',
            'totalRefunded',
            'status',
            'gateway'
        ));
    }

    /**
     * Muestra el detalle completo de un pago y su payload para auditoría.
     */
    public function show(Payment $payment)
    {
        $payment->load('order.user', 'order.event');
        
        return view('admin.payments.show', compact('payment'));
    }
}