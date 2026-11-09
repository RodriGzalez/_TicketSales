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
            <!-- 🌟 BANNER / ENCABEZADO DESTACADO DEL EVENTO -->
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
                <div class="pt-1">
                    <a href="{{ route('events.index') }}" class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl transition text-sm border border-gray-200">
                        ← Volver a los eventos
                    </a>
                </div>
            </form>

        </div>
    </div>

    <div x-data="countdown({{ $order->expires_at ? \Carbon\Carbon::parse($order->expires_at)->timestamp * 1000 : 0 }})" x-init="start()">

    <!-- TU CONTENIDO NORMAL DEL CHECKOUT (Lo puedes difuminar opcionalmente si expira) -->
    <div :class="expired ? 'filter blur-sm pointer-events-none' : ''" class="transition-all duration-300">
        <!-- Aquí va todo tu diseño actual de selección o pago -->
    </div>

    <!-- 🛑 MODAL DE RESERVA EXPIRADA -->
    <div x-show="expired" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
         x-cloak>
        
        <div class="bg-gray-900 border border-gray-800 text-white rounded-3xl p-8 max-w-md w-full text-center shadow-2xl space-y-6 transform transition-all">
            
            <!-- Icono de Reloj / Arena -->
            <div class="w-16 h-16 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto text-2xl shadow-inner">
                ⏳
            </div>

            <!-- Títulos -->
            <div class="space-y-2">
                <h3 class="text-xl font-bold tracking-tight text-white">¡Tu reserva ha expirado!</h3>
                <p class="text-sm text-gray-400 leading-relaxed">
                    El tiempo límite para completar tu compra ha terminado. Los boletos han sido liberados de nuevo al inventario general.
                </p>
            </div>

            <!-- Botón de Acción -->
            <a href="{{ route('events.index') }}" 
               class="w-full inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-indigo-600/30 transition-all text-center">
                Volver a intentar compra
            </a>

        </div>
    </div>
</div>

    <!-- Script del Temporizador -->
    <script>
 function countdown(expiryTimestamp) {
    return {
        timeRemaining: '00:00',
        expired: false,
        start() {
            // Si por alguna razón el timestamp viene vacío o ya pasó, no hacemos nada de inmediato
            if (!expiryTimestamp || expiryTimestamp <= 0) {
                return;
            }

            const updateTimer = () => {
                const now = new Date().getTime();
                const distance = expiryTimestamp - now;

                if (distance <= 0) {
                    this.timeRemaining = '00:00';
                    this.expired = true; // 👈 Ahora sí, solo cuando el tiempo real se agote
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