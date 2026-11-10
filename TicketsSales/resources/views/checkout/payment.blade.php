<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago de Boletos - GraduFest</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Alpine.js necesario para el funcionamiento del temporizador -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen py-12">
       
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        
        <!-- Título y Total -->
        <h2 class="text-2xl font-black text-gray-900 mb-2">Finalizar Compra</h2>
        
        <p class="text-xs text-gray-500 mb-6">Orden #{{ $order->id }} — Total a pagar: <span class="font-bold text-gray-900">${{ number_format($order->total_amount, 2) }} MXN</span></p>
                 <div class="bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] uppercase tracking-wider bg-white/20 px-2.5 py-1 rounded-md font-bold">Checkout Seguro</span>
                    <span class="text-xs font-mono font-semibold">Orden #{{ $order->id }}</span>
                </div>
                
                <!-- Título grande y claro del evento -->
                <h1 class="text-2xl font-black tracking-tight mb-1">
                    {{ $order->items->first()->ticketType->event->title ?? 'Gran Evento de Graduación' }}
                </h1>
                
                <p class="text-indigo-100 text-xs flex items-center gap-1.5">
                    <span>📅</span> {{ $order->items->first()->ticketType->event->date_time ?? 'Fecha por confirmar' }}
                </p>
            </div>

            <div class="p-6 space-y-5">
                
                <!-- 🎟️ DESGLOSE DE BOLETOS SELECCIONADOS -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tus Boletos Seleccionados</div>
                    
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    {{ $item->quantity }}x
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $item->ticketType->name ?? 'Boleto General' }}</p>
                                    <p class="text-xs text-gray-500">Cantidad: {{ $item->quantity }} boleto(s)</p>
                                </div>
                            </div>
                            <div class="text-right font-black text-indigo-600 text-base">
                                ${{ number_format($item->subtotal ?? ($item->quantity * ($item->ticketType->price ?? 0)), 2) }}
                            </div>
                        </div>
                    @endforeach   
        <!-- ⏳ TEMPORIZADOR INTEGRADO (Pasando timestamp en milisegundos desde PHP) -->
        <div x-data="countdown({{ \Carbon\Carbon::parse($order->expires_at)->timestamp * 1000 }})" x-init="start()" class="bg-amber-50/70 border border-amber-200/80 p-3.5 rounded-2xl flex items-center justify-between mb-6 shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="text-lg">⏳</span>
                <div>
                    <h4 class="text-xs font-bold text-amber-900 leading-tight">Tiempo para completar tu pago</h4>
                    <p class="text-[10px] text-amber-700 mt-0.5">Tus boletos se liberarán al expirar.</p>
                </div>
            </div>
            <div class="bg-white px-3 py-1.5 rounded-xl border border-amber-200 shadow-sm">
                <span class="text-sm font-mono font-black text-amber-800 tracking-wider" x-text="timeRemaining">--:--</span>
            </div>
        </div>

        <!-- Formulario de Pago de Stripe -->
        <form id="payment-form" class="space-y-4">
        @csrf
            <div id="payment-element" class="p-3 border border-gray-200 rounded-xl mb-4">
                <!-- Stripe Elements insertará aquí los inputs seguros de la tarjeta -->
            </div>

            <button id="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3.5 rounded-xl transition shadow-lg text-sm">
                <span id="button-text">Pagar Ahora</span>
            </button>
            <div id="error-message" class="text-red-500 text-xs mt-2 text-center"></div>
                    <div class="pt-1">
                    <a href="{{ route('events.index') }}" class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl transition text-sm border border-gray-200">
                        ← Volver a los eventos
                    </a>
                </div>
        </form>
    </div>

    <!-- Script de Stripe y Temporizador -->
    <script>
        // 1. Lógica del temporizador con Alpine.js basada en milisegundos exactos

function countdown(expiryTimestamp) {
    return {
        timeRemaining: '00:00',
        start() {
            const updateTimer = () => {
                const now = new Date().getTime();
                const distance = expiryTimestamp - now;

                if (distance <= 0) {
                    this.timeRemaining = '00:00';
                    
                    // 🔴 Forzar redirección directa a la ruta del evento o al inicio
                    window.location.href = "{{ route('events.index') }}";
                    return;
                }

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                this.timeRemaining = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            };

            updateTimer();
            setInterval(updateTimer, 1000);
        }
    }
}
        // 2. Integración de Stripe Elements
        const stripe = Stripe("{{ $stripeKey }}");
        const clientSecret = "{{ $clientSecret }}";

        const elements = stripe.elements({ clientSecret });
        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            document.getElementById('submit').disabled = true;
            document.getElementById('button-text').textContent = "Procesando...";

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: "{{ route('checkout.success') }}",
                },
            });

            if (error) {
                const messageDiv = document.getElementById('error-message');
                messageDiv.textContent = error.message;
                document.getElementById('submit').disabled = false;
                document.getElementById('button-text').textContent = "Pagar Ahora";
            }
        });
    </script>
</body>
</html>