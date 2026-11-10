@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="ticketScanner()">
    
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-black text-gray-900">Control de Accesos — Escáner con Cámara 📱</h1>
        <p class="text-sm text-gray-500">Apunta con la cámara del celular o tablet al código QR del boleto.</p>
    </div>

    <!-- 📷 CONTENEDOR DE LA CÁMARA QR -->
    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100 mb-6 max-w-xl mx-auto">
        <div id="reader" class="w-full rounded-xl overflow-hidden"></div>
        
        <!-- Botón alternativo manual por si falla la cámara -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <form @submit.prevent="verifyTicket(code)" class="flex gap-3">
                <input type="text" 
                       x-model="code" 
                       placeholder="O escribe el folio manualmente..." 
                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 font-mono">
                
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl transition text-sm">
                    Validar
                </button>
            </form>
        </div>
    </div>

    <!-- 📊 RESULTADO DE LA VALIDACIÓN -->
    <div x-show="result" 
         class="max-w-xl mx-auto rounded-2xl p-6 shadow-xl transition-all duration-300 border mb-6"
         :class="{
            'bg-emerald-50 border-emerald-200 text-emerald-900': resultType === 'success',
            'bg-amber-50 border-amber-200 text-amber-900': resultType === 'warning',
            'bg-rose-50 border-rose-200 text-rose-900': resultType === 'error'
         }" 
         style="display: none;">
        
        <div class="flex items-start gap-4">
            <div class="text-3xl" x-text="resultType === 'success' ? '✅' : (resultType === 'warning' ? '⚠️' : '❌')"></div>
            <div class="flex-1">
                <h3 class="text-lg font-black mb-1" x-text="resultMessage"></h3>
                
                <template x-if="ticketDetails">
                <div class="mt-3 bg-white/90 rounded-xl p-4 text-xs space-y-2 border border-black/5 shadow-sm">
                    <p><strong class="text-gray-600">Folio:</strong> <span class="font-mono font-bold text-gray-900" x-text="ticketDetails.folio"></span></p>
                    <p><strong class="text-gray-600">Nombre:</strong> <span class="font-medium text-gray-900" x-text="ticketDetails.name"></span></p>
                    <p><strong class="text-gray-600">Descripción Evento:</strong> <span class="text-gray-900" x-text="ticketDetails.event_description"></span></p>
                    <p><strong class="text-gray-600">Cantidad y tipo:</strong> <span class="text-gray-900" x-text="ticketDetails.quantity_and_type"></span></p>
                    <p><strong class="text-gray-600">Atendido por:</strong> <span class="text-gray-900" x-text="ticketDetails.staff_name"></span></p>
                </div>
            </template>
            </div>
        </div>
    </div>

</div>

<!-- 📦 CARGAR LIBRERÍA DE LECTURA QR DESDE CDN -->
<!-- 📦 CARGAR LIBRERÍA DE LECTURA QR DESDE CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
function ticketScanner() {
    return {
        code: '',
        result: false,
        resultType: 'success',
        resultMessage: '',
        ticketDetails: null,
        scanner: null,
        init() {
            // Inicializar la cámara trasera del celular/tablet automáticamente
            this.scanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: { width: 250, height: 250 } 
            }, false);

            this.scanner.render((decodedText, decodedResult) => {
                // Cuando detecta un QR exitosamente:
                this.verifyTicket(decodedText);
            }, (errorMessage) => {
                // Errores de lectura cuadro por cuadro (se ignoran)
            });
        },
        async verifyTicket(ticketCode) {
            if(!ticketCode || !ticketCode.trim()) return;
            this.code = ticketCode;

            // Obtener el token CSRF actualizado desde el DOM o meta tag
            let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                let response = await fetch("{{ route('admin.scanner.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: ticketCode })
                });

                let data = await response.json();

                this.result = true;
                if (response.ok) {
                    this.resultType = data.status || 'success'; 
                    this.resultMessage = data.message || 'Boleto verificado correctamente.';
                    this.ticketDetails = data.ticket || null;
                } else {
                    this.resultType = 'error';
                    this.resultMessage = data.message || 'El servidor rechazó la solicitud.';
                    this.ticketDetails = null;
                }
                this.code = '';

            } catch (err) {
                console.error("Error al procesar el código:", err);
                this.result = true;
                this.resultType = 'error';
                this.resultMessage = 'Error de conexión con el servidor. Verifica tu red.';
                this.ticketDetails = null;
            }
        }
    }
}
</script>
@endsection