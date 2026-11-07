<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Selección de Pago') }}
        </h2>
    </x-slot>

    <div class="py-12 flex justify-center items-center">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-xl overflow-hidden p-8 border border-gray-100">
            
            <!-- Título -->
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Elige tu Método de Pago</h3>
                <p class="text-xs text-gray-500 mt-1">Orden #{{ $order->id }} — Total: <span class="font-bold text-gray-800">${{ number_format($order->total_amount, 2) }} MXN</span></p>
            </div>

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

            <!-- Formulario de Selección -->
            <form action="{{ route('checkout.process-selection', $order->id) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Opción Tarjeta -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-600 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                    <input type="radio" name="payment_method" value="stripe" class="text-indigo-600 focus:ring-indigo-500 h-4 w-4" checked>
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-gray-900">Tarjeta de Crédito / Débito</span>
                        <span class="block text-xs text-gray-500">Pago seguro en línea mediante Stripe</span>
                    </div>
                </label>

                <!-- Opción Contra Entrega -->
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-600 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                    <input type="radio" name="payment_method" value="cash_on_delivery" class="text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-gray-900">Pago Contra Entrega</span>
                        <span class="block text-xs text-gray-500">Paga en efectivo al recoger tus boletos o en la entrada</span>
                    </div>
                </label>

                <!-- Botón Continuar -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transition">
                        Continuar con el Pago
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Script del Temporizador -->
    <script>
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
    </script>
</x-app-layout>