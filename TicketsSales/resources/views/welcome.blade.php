<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GraduFest Tickets - Plataforma de Boletinaje Escolar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter & Montserrat) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <!-- QR Code Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#1e1b4b',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-overlay {
            background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.85) 60%, rgba(15,23,42,1) 100%);
        }
        .hero-gradient {
            background: linear-gradient(90deg, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.6) 50%, rgba(15,23,42,0.1) 100%);
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans bg-slate-950 text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- HEADER DE NAVEGACIÓN PRINCIPAL -->
    <header class="sticky top-0 z-40 w-full glass-panel border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center gap-3 cursor-pointer" onclick="navigateTo('plp')">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-400 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid font-bold fa-ticket text-xl text-white transform -rotate-12"></i>
                    </div>
                    <div>
                        <span class="font-heading font-extrabold text-xl tracking-tight bg-gradient-to-r from-white via-slate-200 to-amber-300 bg-clip-text text-transparent">
                            GraduFest
                        </span>
                        <span class="block text-[10px] tracking-widest text-indigo-400 font-semibold uppercase">Tickets Escolares</span>
                    </div>
                </div>

                <!-- Buscador de eventos (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <input type="text" id="searchInput" oninput="filterEvents()" placeholder="Buscar tu escuela, universidad o evento..." 
                            class="w-full bg-slate-900/90 border border-slate-700/70 text-slate-200 rounded-full py-2.5 pl-11 pr-4 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder:text-slate-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                    </div>
                </div>

                <!-- Menú Derecha: Google Auth & Mis Boletos -->
                <div class="flex items-center gap-4">
                    <button onclick="navigateTo('my-tickets')" class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 text-sm font-medium transition-all">
                        <i class="fa-solid fa-wallet text-indigo-400"></i>
                        <span>Mis Boletos</span>
                    </button>

                    <!-- Google Auth Trigger -->
                    <div id="authContainer">
                        <button id="btnGoogleAuth" onclick="toggleGoogleAuth()" class="flex items-center gap-2.5 bg-white text-slate-800 font-semibold text-sm px-4 py-2 rounded-full hover:bg-slate-100 transition-all shadow-md hover:shadow-lg hover:shadow-white/10 active:scale-95">
                            <svg class="w-4 h-4" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Entrar con Google</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categorías de Navegación -->
            <nav class="flex items-center space-x-1 sm:space-x-4 py-2.5 overflow-x-auto border-t border-slate-800/60 no-scrollbar text-xs sm:text-sm font-medium">
                <button onclick="filterCategory('all')" class="cat-btn px-4 py-1.5 rounded-full bg-indigo-600 text-white font-semibold transition-all shadow-sm shadow-indigo-500/30 whitespace-nowrap" data-cat="all">
                    <i class="fa-solid fa-graduation-cap mr-1.5"></i>Todos los Eventos
                </button>
                <button onclick="filterCategory('Universidad')" class="cat-btn px-4 py-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-all whitespace-nowrap" data-cat="Universidad">
                    <i class="fa-solid fa-university mr-1.5"></i>Universidades
                </button>
                <button onclick="filterCategory('Bachillerato')" class="cat-btn px-4 py-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-all whitespace-nowrap" data-cat="Bachillerato">
                    <i class="fa-solid fa-school mr-1.5"></i>Bachilleratos / Preparatorias
                </button>
                <button onclick="filterCategory('Secundaria')" class="cat-btn px-4 py-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-all whitespace-nowrap" data-cat="Secundaria">
                    <i class="fa-solid fa-child-reaching mr-1.5"></i>Secundarias & Primarias
                </button>
            </nav>
        </div>
    </header>

    <!-- BANNER DE RESERVA EXPIRADA (Estilo Boletia) -->
    <div id="reservationTimerBar" class="hidden sticky top-20 z-30 w-full bg-gradient-to-r from-amber-600 via-rose-600 to-indigo-700 text-white py-2.5 px-4 shadow-lg border-b border-amber-400/30 animate-pulse">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between text-xs sm:text-sm gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock text-base animate-bounce"></i>
                <span class="font-semibold">¡Boletos reservados temporalmente!</span>
                <span class="hidden md:inline text-amber-100">Completa tu pago antes de que expire el tiempo.</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-slate-950/60 px-3 py-1 rounded-md font-mono font-bold text-amber-300 text-sm border border-amber-400/40">
                    <span id="timerMinutes">05</span>:<span id="timerSeconds">00</span>
                </div>
                <button onclick="forceExpireTimer()" class="text-[11px] underline text-amber-200 hover:text-white bg-slate-900/40 px-2 py-1 rounded border border-white/20">
                    (Probar Expiración)
                </button>
            </div>
        </div>
    </div>

    <!-- VISTA PRINCIPAL (PLP & BANNER) -->
    <main id="mainContainer" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-10">

        <!-- VIEW: PLP (CATÁLOGO) -->
        <section id="viewPlp" class="space-y-10">
            
            <!-- HERO BANNER CARRUSEL (Estilo Ticketmaster) -->
            <div class="relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl h-[380px] sm:h-[450px] group">
                <!-- Slide Images -->
                <div id="heroCarousel" class="absolute inset-0 transition-all duration-700 ease-in-out bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=1600');">
                    <!-- Overlays -->
                    <div class="absolute inset-0 hero-gradient"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                </div>

                <!-- Carousel Content -->
                <div class="relative h-full max-w-2xl flex flex-col justify-end p-6 sm:p-10 z-10 space-y-3">
                    <div class="flex items-center gap-2">
                        <span id="heroCategory" class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 text-xs font-bold uppercase tracking-wider">
                            Gala Destacada
                        </span>
                        <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 text-xs font-semibold">
                            Últimos Lugares
                        </span>
                    </div>
                    <h1 id="heroTitle" class="font-heading font-black text-2xl sm:text-4xl lg:text-5xl text-white tracking-tight leading-tight">
                        Gran Gala de Graduación ITESM 2026
                    </h1>
                    <p id="heroSubtitle" class="text-slate-300 text-xs sm:text-sm line-clamp-2 max-w-xl">
                        Celebra la culminación de tus estudios en una noche inolvidable en el Expo Center. Cena de 3 tiempos, orquesta en vivo y recepción alfombra roja.
                    </p>
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <button id="heroBtn" onclick="openPdp(1)" class="px-6 py-3 rounded-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 text-white font-bold text-sm hover:opacity-95 transition-all shadow-lg shadow-indigo-500/30 flex items-center gap-2 active:scale-95">
                            <span>Comprar Boletos Ahora</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                        <div class="text-xs text-slate-400">
                            <i class="fa-solid fa-calendar text-amber-400 mr-1"></i> <span id="heroDate">15 de Julio, 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Controles del Carrusel -->
                <div class="absolute bottom-6 right-6 flex items-center gap-3 z-20">
                    <button onclick="prevSlide()" class="w-10 h-10 rounded-full glass-panel hover:bg-slate-800 text-white flex items-center justify-center transition-all border border-slate-700">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div id="carouselDots" class="flex gap-1.5">
                        <!-- Generado dinámicamente -->
                    </div>
                    <button onclick="nextSlide()" class="w-10 h-10 rounded-full glass-panel hover:bg-slate-800 text-white flex items-center justify-center transition-all border border-slate-700">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- SECCIÓN CATÁLOGO DE EVENTOS (GRID) -->
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading font-bold text-2xl text-white flex items-center gap-2">
                            <i class="fa-solid fa-sparkles text-amber-400"></i>
                            Próximas Graduaciones & Eventos
                        </h2>
                        <p class="text-slate-400 text-sm">Selecciona tu institución para reservar boletos para ti y tus acompañantes.</p>
                    </div>
                    <div class="text-xs text-slate-400 bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800 self-start sm:self-auto">
                        Mostrando <span id="eventCount" class="font-bold text-white">0</span> eventos disponibles
                    </div>
                </div>

                <!-- Grid de Tarjetas PLP -->
                <div id="eventsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Renderizado dinámicamente con JavaScript -->
                </div>
            </div>
        </section>

        <!-- VIEW: PDP (DETALLE DE EVENTO / CARTELERA TEATRO) -->
        <section id="viewPdp" class="hidden space-y-8">
            <!-- Breadcrumb -->
            <button onclick="navigateTo('plp')" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Volver a la cartelera de eventos</span>
            </button>

            <!-- Card Principal Detalle del Evento -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Columna Izquierda: Imagen Banner & Detalles -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="relative rounded-3xl overflow-hidden border border-slate-800 bg-slate-900 h-64 sm:h-80 shadow-xl">
                        <img id="pdpImage" src="" alt="Evento" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span id="pdpCategory" class="px-3 py-1 rounded-full bg-indigo-600/90 text-white font-bold text-xs uppercase tracking-wider backdrop-blur-md"></span>
                            <span id="pdpAvailability" class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-semibold text-xs backdrop-blur-md"></span>
                        </div>
                    </div>

                    <div class="glass-panel rounded-2xl p-6 sm:p-8 space-y-4">
                        <h1 id="pdpTitle" class="font-heading font-black text-2xl sm:text-3xl text-white"></h1>
                        
                        <!-- Metadata Badges -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3 border-y border-slate-800/80 text-sm">
                            <div class="flex items-center gap-3 text-slate-300">
                                <div class="w-9 h-9 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div>
                                    <span class="block text-xs text-slate-400">Fecha y Hora</span>
                                    <span id="pdpDate" class="font-semibold text-white"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-slate-300">
                                <div class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <span class="block text-xs text-slate-400">Lugar del Evento</span>
                                    <span id="pdpLocation" class="font-semibold text-white"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <h3 class="font-bold text-white mb-2">Acerca de esta celebración</h3>
                            <p id="pdpDescription" class="text-slate-300 text-sm leading-relaxed"></p>
                        </div>

                        <!-- Límite por usuario aviso -->
                        <div class="p-3.5 rounded-xl bg-indigo-950/40 border border-indigo-500/30 flex items-center gap-3 text-xs text-indigo-300">
                            <i class="fa-solid fa-shield-halved text-base text-indigo-400"></i>
                            <span>Límite de compra: <strong>Máximo 5 boletos por usuario</strong> para asegurar acceso a todas las familias.</span>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Selector de Boletos & Resumen (Estilo Taquilla) -->
                <div class="lg:col-span-5">
                    <div class="sticky top-28 glass-panel rounded-2xl p-6 space-y-6 border border-slate-700/80 shadow-2xl">
                        <div class="border-b border-slate-800 pb-4">
                            <h2 class="font-heading font-bold text-xl text-white flex items-center justify-between">
                                <span>Selección de Boletos</span>
                                <i class="fa-solid fa-ticket-simple text-amber-400"></i>
                            </h2>
                            <p class="text-slate-400 text-xs mt-1">Elige los pases que necesitas para tu graduación.</p>
                        </div>

                        <!-- Selectores de Boletos -->
                        <div class="space-y-4">
                            <!-- Boleto Adulto -->
                            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-white text-sm">Boleto Adulto</div>
                                    <div class="text-xs text-slate-400">Cena 3 tiempos + Acceso Gala</div>
                                    <div class="text-indigo-400 font-bold text-sm mt-1">$850.00 MXN</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button onclick="updateTicketCount('adult', -1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-all disabled:opacity-40">
                                        -
                                    </button>
                                    <span id="qtyAdult" class="font-mono font-bold text-white text-base w-6 text-center">0</span>
                                    <button onclick="updateTicketCount('adult', 1)" class="w-8 h-8 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold flex items-center justify-center transition-all">
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Boleto Niño -->
                            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-white text-sm">Boleto Niño / Menor</div>
                                    <div class="text-xs text-slate-400">Menú infantil + Área de juegos</div>
                                    <div class="text-indigo-400 font-bold text-sm mt-1">$450.00 MXN</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button onclick="updateTicketCount('child', -1)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-all disabled:opacity-40">
                                        -
                                    </button>
                                    <span id="qtyChild" class="font-mono font-bold text-white text-base w-6 text-center">0</span>
                                    <button onclick="updateTicketCount('child', 1)" class="w-8 h-8 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold flex items-center justify-center transition-all">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de Costos -->
                        <div class="space-y-2 pt-2 border-t border-slate-800 text-xs sm:text-sm">
                            <div class="flex justify-between text-slate-400">
                                <span>Total Boletos:</span>
                                <span id="summaryTotalTickets" class="font-semibold text-white">0</span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Cargo por Servicio (5%):</span>
                                <span id="summaryServiceFee" class="font-semibold text-white">$0.00 MXN</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-slate-800">
                                <span>Total a Pagar:</span>
                                <span id="summaryGrandTotal" class="text-amber-400 text-lg">$0.00 MXN</span>
                            </div>
                        </div>

                        <!-- Botón Continuar a Pago -->
                        <button id="btnProceedCheckout" onclick="startCheckout()" disabled class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-600 text-white font-bold text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Reservar y Pagar</span>
                        </button>

                        <div class="text-[11px] text-center text-slate-400">
                            <i class="fa-solid fa-shield-cat text-emerald-400 mr-1"></i> Transacción segura protegida con cifrado bancario.
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- VIEW: PASARELA DE PAGO (SIMULACIÓN) -->
        <section id="viewCheckout" class="hidden max-w-2xl mx-auto space-y-6">
            <button onclick="navigateTo('pdp')" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Modificar selección de boletos</span>
            </button>

            <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-6 border border-slate-700/80 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading font-bold text-xl text-white">Pasarela de Pago Segura</h2>
                        <p class="text-xs text-slate-400">Ingresa los datos para emitir tus pases digitales.</p>
                    </div>
                    <div class="flex gap-2 text-xl text-slate-400">
                        <i class="fa-brands fa-cc-visa text-blue-400"></i>
                        <i class="fa-brands fa-cc-mastercard text-rose-400"></i>
                        <i class="fa-brands fa-cc-amex text-sky-400"></i>
                    </div>
                </div>

                <!-- Resumen Exprés -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex justify-between items-center text-sm">
                    <div>
                        <span class="block text-xs text-slate-400">Evento Seleccionado</span>
                        <span id="checkoutEventTitle" class="font-bold text-white"></span>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs text-slate-400">Total a Cargar</span>
                        <span id="checkoutTotalAmount" class="font-bold text-amber-400 text-base"></span>
                    </div>
                </div>

                <!-- Formulario de Tarjeta Simulado -->
                <form id="paymentForm" onsubmit="processPayment(event)" class="space-y-4">
                @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nombre en la Tarjeta</label>
                        <input type="text" required placeholder="Ej. Juan Pérez González" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Número de Tarjeta</label>
                        <div class="relative">
                            <input type="text" maxlength="19" required placeholder="4532 •••• •••• 8901" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono">
                            <i class="fa-solid fa-credit-card absolute right-3 top-3.5 text-slate-500"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Vencimiento</label>
                            <input type="text" placeholder="MM/AA" maxlength="5" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono text-center">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">CVV / CVC</label>
                            <input type="password" maxlength="4" placeholder="123" required class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-indigo-500 font-mono text-center">
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-4 py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-base shadow-xl shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Confirmar Pago y Emitir Boletos</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- VIEW: CONFIRMACIÓN Y BOLETO DIGITAL CON GOOGLE WALLET -->
        <section id="viewSuccess" class="hidden max-w-xl mx-auto space-y-6 text-center">
            
            <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-6 border border-emerald-500/30 shadow-2xl relative overflow-hidden">
                <!-- Ribbon Éxito -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-circle-check"></i> ¡Compra Confirmada!
                </div>

                <h2 class="font-heading font-black text-2xl text-white">¡Tus boletos están listos!</h2>
                <p class="text-xs text-slate-300">Hemos enviado la confirmación a tu correo. Presenta el siguiente código QR en la entrada del evento.</p>

                <!-- Tarjeta del Boleto Digital -->
                <div class="bg-gradient-to-b from-slate-900 to-slate-950 rounded-2xl p-6 border border-slate-800 text-left space-y-4 shadow-inner relative">
                    <div class="flex justify-between items-start border-b border-slate-800 pb-3">
                        <div>
                            <h3 id="ticketEventTitle" class="font-bold text-white text-base"></h3>
                            <p id="ticketCategory" class="text-xs text-indigo-400 font-semibold"></p>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-amber-400/20 text-amber-300 text-[10px] font-bold">ACCESO VALIDO</span>
                    </div>

                    <!-- Código QR Centrado -->
                    <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-md my-2">
                        <div id="qrcode" class="p-2"></div>
                        <span id="ticketUuid" class="font-mono text-[10px] text-slate-700 font-bold mt-2"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-800 pt-3 text-slate-400">
                        <div>
                            <span class="block text-[10px] uppercase">Titular</span>
                            <strong id="ticketHolder" class="text-slate-200">Invitado GraduFest</strong>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase">Boletos Adquiridos</span>
                            <strong id="ticketQtyDetails" class="text-slate-200"></strong>
                        </div>
                    </div>
                </div>

                <!-- Botón OFICIAL Google Wallet -->
                <div class="pt-2">
                    <button onclick="addToGoogleWallet()" class="w-full py-3 px-6 rounded-full bg-black hover:bg-slate-900 text-white border border-slate-700 font-semibold text-sm shadow-xl flex items-center justify-center gap-3 group transition-all">
                        <!-- Icono oficial de Google Wallet -->
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M20.5 5h-17A1.5 1.5 0 0 0 2 6.5v11A1.5 1.5 0 0 0 3.5 19h17a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 20.5 5z"/>
                            <path fill="#34A853" d="M22 8.5H2v7h20v-7z"/>
                            <path fill="#FBBC05" d="M22 6.5A1.5 1.5 0 0 0 20.5 5H3.5A1.5 1.5 0 0 0 2 6.5V9h20V6.5z"/>
                            <path fill="#EA4335" d="M2 15.5v2A1.5 1.5 0 0 0 3.5 19h17a1.5 1.5 0 0 0 1.5-1.5v-2H2z"/>
                        </svg>
                        <span>Añadir a Google Wallet</span>
                    </button>
                </div>

                <div class="pt-2">
                    <button onclick="navigateTo('plp')" class="text-xs text-slate-400 hover:text-white underline">
                        Regresar a la página principal
                    </button>
                </div>
            </div>
        </section>

        <!-- VIEW: MIS BOLETOS (USER DASHBOARD) -->
        <section id="viewMyTickets" class="hidden space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="font-heading font-bold text-2xl text-white">Mis Boletos Adquiridos</h2>
                    <p class="text-slate-400 text-sm">Gestiona tus accesos para los eventos de graduación.</p>
                </div>
                <button onclick="navigateTo('plp')" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-500 transition-all">
                    + Explorar más eventos
                </button>
            </div>

            <div id="myTicketsList" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Se poblará con JS al tener compras -->
            </div>
        </section>

    </main>

    <!-- FOOTER PRINCIPAL -->
    <footer class="mt-auto border-t border-slate-800/80 bg-slate-950 py-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-ticket text-indigo-500"></i>
                <span class="font-bold text-slate-300">GraduFest Tickets Platform v1.0</span>
            </div>
            <p>© 2026 GraduFest. Todos los derechos reservados. Desarrollado para eventos académicos.</p>
        </div>
    </footer>

    <!-- MODAL EXPIRACIÓN DE RESERVA -->
    <div id="modalExpired" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="glass-panel max-w-md w-full rounded-3xl p-6 sm:p-8 text-center space-y-5 border border-rose-500/40 shadow-2xl animate-scaleIn">
            <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center mx-auto text-2xl">
                <i class="fa-solid fa-hourglass-end"></i>
            </div>
            <h3 class="font-heading font-bold text-2xl text-white">¡Tu reserva ha expirado!</h3>
            <p class="text-slate-300 text-xs sm:text-sm">El tiempo límite de 5 minutos para completar tu compra ha terminado. Los boletos han sido liberados de nuevo al inventario general.</p>
            <button onclick="resetAndReturnToPdp()" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all">
                Volver a intentar compra
            </button>
        </div>
    </div>

    <!-- MODAL SIMULACIÓN GOOGLE WALLET -->
    <div id="modalWallet" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="bg-slate-900 border border-slate-700 max-w-sm w-full rounded-3xl p-6 text-center space-y-4 shadow-2xl relative">
            <button onclick="closeWalletModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-emerald-400 text-xl border border-slate-700">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <h3 class="font-heading font-bold text-lg text-white">¡Añadido a Google Wallet!</h3>
            <p class="text-xs text-slate-300">El pase digital con el código QR ha sido sincronizado exitosamente en la aplicación de Google Wallet de tu dispositivo.</p>
            <div class="p-3 bg-slate-950 rounded-xl text-[11px] text-emerald-400 font-semibold border border-emerald-500/20">
                <i class="fa-solid fa-check mr-1"></i> Disponible para acceso offline sin internet
            </div>
            <button onclick="closeWalletModal()" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition-all">
                Entendido
            </button>
        </div>
    </div>


    <!-- JAVASCRIPT LOGIC -->
    <script>
        // BASE DE DATOS DE EVENTOS SIMULADA
        const EVENTS_DATA = [
            {
                id: 1,
                title: "Gran Gala de Graduación ITESM 2026",
                category: "Universidad",
                institution: "Tecnológico de Monterrey",
                date: "15 de Julio, 2026 - 20:00 hrs",
                location: "Expo Center Metropolitano, Salon Reales",
                image: "https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=800",
                description: "La velada magna de graduación para los graduandos de las facultades de Ingeniería y Negocios. Incluye alfombra roja, banquete Gourmet de 3 tiempos, grupo de música en vivo y pirotecnia.",
                priceRange: "$450 - $850 MXN",
                available: true,
                badge: "Más Vendido"
            },
            {
                id: 2,
                title: "Noche de Gala Preparatoria Anáhuac",
                category: "Bachillerato",
                institution: "Prepa Anáhuac Campus Norte",
                date: "20 de Junio, 2026 - 19:30 hrs",
                location: "Hotel Fiesta Americana, Gran Salón",
                image: "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=800",
                description: "Fiesta formal de fin de cursos para la generación 2023-2026. Brindis de honor, DJ en vivo, cabina de fotos 360 y cena de gala.",
                priceRange: "$450 - $850 MXN",
                available: true,
                badge: "Pocos Lugares"
            },
            {
                id: 3,
                title: "Ceremonia y Baile Secundaria Colegio Americano",
                category: "Secundaria",
                institution: "American School Foundation",
                date: "28 de Junio, 2026 - 18:00 hrs",
                location: "Club de Golf Los Encinos",
                image: "https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&q=80&w=800",
                description: "Evento familiar de graduación para alumnos de 3ro de Secundaria. Recepción de bienvenida, cena buffet internacional y show de luces.",
                priceRange: "$450 - $850 MXN",
                available: true,
                badge: "Confirmado"
            },
            {
                id: 4,
                title: "Gala de Graduación Medicina UNAM 2026",
                category: "Universidad",
                institution: "Universidad Nacional Autónoma de México",
                date: "05 de Agosto, 2026 - 21:00 hrs",
                location: "Centro de Convenciones WTC",
                image: "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=800",
                description: "Celebración oficial para la generación de Médicos Cirujanos. Orquesta sinfónica de bienvenida y cena baile hasta el amanecer.",
                priceRange: "$450 - $850 MXN",
                available: true,
                badge: "Destacado"
            }
        ];

        // ESTADO GLOBAL DE LA APLICACIÓN
        let currentUser = null;
        let selectedEvent = null;
        let ticketQty = { adult: 0, child: 0 };
        const MAX_TICKETS_PER_USER = 5;
        const TICKET_PRICES = { adult: 850, child: 450 };
        
        // Timer Logic
        let timerInterval = null;
        let timerSecondsTotal = 300; // 5 Minutos

        // User Purchases Store
        let userPurchases = [];

        // INICIALIZACIÓN DE LA PÁGINA
        window.onload = function() {
            renderHeroCarousel();
            renderEventsGrid(EVENTS_DATA);
        };

        // NAVEGACIÓN ENTRE VISTAS (SPA)
        function navigateTo(viewName) {
            document.getElementById('viewPlp').classList.add('hidden');
            document.getElementById('viewPdp').classList.add('hidden');
            document.getElementById('viewCheckout').classList.add('hidden');
            document.getElementById('viewSuccess').classList.add('hidden');
            document.getElementById('viewMyTickets').classList.add('hidden');

            if (viewName === 'plp') {
                document.getElementById('viewPlp').classList.remove('hidden');
            } else if (viewName === 'pdp') {
                document.getElementById('viewPdp').classList.remove('hidden');
            } else if (viewName === 'checkout') {
                document.getElementById('viewCheckout').classList.remove('hidden');
            } else if (viewName === 'success') {
                document.getElementById('viewSuccess').classList.remove('hidden');
            } else if (viewName === 'my-tickets') {
                document.getElementById('viewMyTickets').classList.remove('hidden');
                renderMyTickets();
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // GOOGLE AUTHENTICATION SIMULADA
        function toggleGoogleAuth() {
            const container = document.getElementById('authContainer');
            if (!currentUser) {
                currentUser = {
                    name: "Carlos Mendoza",
                    email: "carlos.mendoza@gmail.com",
                    avatar: "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=120"
                };

                container.innerHTML = `
                    <div class="flex items-center gap-3 bg-slate-900 border border-slate-700/80 rounded-full py-1 px-3">
                        <img src="${currentUser.avatar}" alt="Avatar" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-xs font-semibold text-slate-200 hidden md:inline">${currentUser.name}</span>
                        <button onclick="toggleGoogleAuth()" class="text-slate-400 hover:text-rose-400 text-xs ml-1" title="Cerrar Sesión">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </div>
                `;
            } else {
                currentUser = null;
                container.innerHTML = `
                    <button id="btnGoogleAuth" onclick="toggleGoogleAuth()" class="flex items-center gap-2.5 bg-white text-slate-800 font-semibold text-sm px-4 py-2 rounded-full hover:bg-slate-100 transition-all shadow-md">
                        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        <span>Entrar con Google</span>
                    </button>
                `;
            }
        }

        // HERO BANNER CARRUSEL
        let currentSlide = 0;
        function renderHeroCarousel() {
            const dotsContainer = document.getElementById('carouselDots');
            dotsContainer.innerHTML = EVENTS_DATA.map((_, idx) => `
                <button onclick="goToSlide(${idx})" class="w-2.5 h-2.5 rounded-full transition-all ${idx === 0 ? 'bg-amber-400 w-6' : 'bg-white/40'}"></button>
            `).join('');
            updateHeroSlide(0);
        }

        function updateHeroSlide(index) {
            currentSlide = index;
            const event = EVENTS_DATA[currentSlide];
            document.getElementById('heroCarousel').style.backgroundImage = `url('${event.image}')`;
            document.getElementById('heroTitle').innerText = event.title;
            document.getElementById('heroSubtitle').innerText = event.description;
            document.getElementById('heroDate').innerText = event.date;
            document.getElementById('heroCategory').innerText = event.category;
            document.getElementById('heroBtn').setAttribute('onclick', `openPdp(${event.id})`);

            const dots = document.getElementById('carouselDots').children;
            Array.from(dots).forEach((dot, idx) => {
                dot.className = `h-2.5 rounded-full transition-all ${idx === currentSlide ? 'bg-amber-400 w-6' : 'bg-white/40 w-2.5'}`;
            });
        }

        function nextSlide() {
            updateHeroSlide((currentSlide + 1) % EVENTS_DATA.length);
        }
        function prevSlide() {
            updateHeroSlide((currentSlide - 1 + EVENTS_DATA.length) % EVENTS_DATA.length);
        }
        function goToSlide(idx) {
            updateHeroSlide(idx);
        }

        // FILTRADO Y BÚSQUEDA PLP
        function filterCategory(cat) {
            document.querySelectorAll('.cat-btn').forEach(btn => {
                if (btn.dataset.cat === cat) {
                    btn.className = "cat-btn px-4 py-1.5 rounded-full bg-indigo-600 text-white font-semibold transition-all shadow-sm shadow-indigo-500/30 whitespace-nowrap";
                } else {
                    btn.className = "cat-btn px-4 py-1.5 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-all whitespace-nowrap";
                }
            });

            const filtered = cat === 'all' ? EVENTS_DATA : EVENTS_DATA.filter(e => e.category === cat);
            renderEventsGrid(filtered);
        }

        function filterEvents() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const filtered = EVENTS_DATA.filter(e => 
                e.title.toLowerCase().includes(query) || 
                e.institution.toLowerCase().includes(query) ||
                e.location.toLowerCase().includes(query)
            );
            renderEventsGrid(filtered);
        }

        function renderEventsGrid(events) {
            const grid = document.getElementById('eventsGrid');
            document.getElementById('eventCount').innerText = events.length;

            if (events.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12 glass-panel rounded-2xl">
                        <i class="fa-solid fa-graduation-cap text-4xl text-slate-600 mb-3"></i>
                        <p class="text-slate-400 font-medium">No se encontraron eventos coincidentes.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = events.map(event => `
                <div class="glass-panel rounded-2xl overflow-hidden border border-slate-800 hover:border-indigo-500/50 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <!-- Imagen Portada -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="${event.image}" alt="${event.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-900/80 text-amber-300 text-[11px] font-bold border border-amber-400/30 backdrop-blur-md">
                                ${event.badge}
                            </span>
                        </div>
                        
                        <!-- Info Content -->
                        <div class="p-5 space-y-3">
                            <span class="text-[11px] font-bold text-indigo-400 uppercase tracking-wider">${event.category} • ${event.institution}</span>
                            <h3 class="font-heading font-bold text-lg text-white group-hover:text-amber-300 transition-colors line-clamp-1">${event.title}</h3>
                            
                            <div class="space-y-1.5 text-xs text-slate-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar text-indigo-400"></i>
                                    <span>${event.date}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-indigo-400"></i>
                                    <span class="line-clamp-1">${event.location}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Tarjeta -->
                    <div class="p-5 pt-0 flex items-center justify-between border-t border-slate-800/60 mt-4">
                        <div>
                            <span class="block text-[10px] text-slate-500 uppercase">Boletos desde</span>
                            <span class="font-bold text-white text-sm">${event.priceRange}</span>
                        </div>
                        <button onclick="openPdp(${event.id})" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all shadow-md shadow-indigo-600/20 active:scale-95">
                            Ver Detalle
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // ABRIR PDP (DETALLE DE EVENTO)
        function openPdp(eventId) {
            selectedEvent = EVENTS_DATA.find(e => e.id === eventId);
            if (!selectedEvent) return;

            document.getElementById('pdpImage').src = selectedEvent.image;
            document.getElementById('pdpTitle').innerText = selectedEvent.title;
            document.getElementById('pdpCategory').innerText = selectedEvent.category;
            document.getElementById('pdpAvailability').innerText = selectedEvent.available ? "Boletos Disponibles" : "Agotado";
            document.getElementById('pdpDate').innerText = selectedEvent.date;
            document.getElementById('pdpLocation').innerText = selectedEvent.location;
            document.getElementById('pdpDescription').innerText = selectedEvent.description;

            // Reset Quantities
            ticketQty = { adult: 0, child: 0 };
            updateTicketSummary();

            navigateTo('pdp');
        }

        // ACTUALIZAR CONTADORES DE BOLETOS
        function updateTicketCount(type, delta) {
            const currentTotal = ticketQty.adult + ticketQty.child;
            
            if (delta > 0 && currentTotal >= MAX_TICKETS_PER_USER) {
                alert(`Solo puedes seleccionar un máximo de ${MAX_TICKETS_PER_USER} boletos por compra.`);
                return;
            }

            if (type === 'adult') {
                ticketQty.adult = Math.max(0, ticketQty.adult + delta);
                document.getElementById('qtyAdult').innerText = ticketQty.adult;
            } else if (type === 'child') {
                ticketQty.child = Math.max(0, ticketQty.child + delta);
                document.getElementById('qtyChild').innerText = ticketQty.child;
            }

            updateTicketSummary();
        }

        function updateTicketSummary() {
            const totalTickets = ticketQty.adult + ticketQty.child;
            const subtotal = (ticketQty.adult * TICKET_PRICES.adult) + (ticketQty.child * TICKET_PRICES.child);
            const serviceFee = subtotal * 0.05;
            const grandTotal = subtotal + serviceFee;

            document.getElementById('summaryTotalTickets').innerText = totalTickets;
            document.getElementById('summaryServiceFee').innerText = `$${serviceFee.toFixed(2)} MXN`;
            document.getElementById('summaryGrandTotal').innerText = `$${grandTotal.toFixed(2)} MXN`;

            const btnCheckout = document.getElementById('btnProceedCheckout');
            if (totalTickets > 0) {
                btnCheckout.disabled = false;
            } else {
                btnCheckout.disabled = true;
            }
        }

        // INICIAR RESERVA & TEMPORIZADOR (Estilo Boletia)
        function startCheckout() {
            if (!currentUser) {
                toggleGoogleAuth(); // Auto login para agilizar demo
            }

            // Mostrar Barra Timer Expiración
            document.getElementById('reservationTimerBar').classList.remove('hidden');
            startReservationTimer();

            // Preparar datos Checkout
            document.getElementById('checkoutEventTitle').innerText = selectedEvent.title;
            const subtotal = (ticketQty.adult * TICKET_PRICES.adult) + (ticketQty.child * TICKET_PRICES.child);
            const grandTotal = subtotal * 1.05;
            document.getElementById('checkoutTotalAmount').innerText = `$${grandTotal.toFixed(2)} MXN`;

            navigateTo('checkout');
        }

        function startReservationTimer() {
            clearInterval(timerInterval);
            timerSecondsTotal = 300; // 5 Minutos

            timerInterval = setInterval(() => {
                timerSecondsTotal--;
                
                const mins = Math.floor(timerSecondsTotal / 60);
                const secs = timerSecondsTotal % 60;

                document.getElementById('timerMinutes').innerText = mins < 10 ? '0' + mins : mins;
                document.getElementById('timerSeconds').innerText = secs < 10 ? '0' + secs : secs;

                if (timerSecondsTotal <= 0) {
                    forceExpireTimer();
                }
            }, 1000);
        }

        function forceExpireTimer() {
            clearInterval(timerInterval);
            document.getElementById('reservationTimerBar').classList.add('hidden');
            document.getElementById('modalExpired').classList.remove('hidden');
        }

        function resetAndReturnToPdp() {
            document.getElementById('modalExpired').classList.add('hidden');
            ticketQty = { adult: 0, child: 0 };
            updateTicketSummary();
            navigateTo('pdp');
        }

        // PROCESAR PAGO Y GENERAR BOLETO
        function processPayment(e) {
            e.preventDefault();
            clearInterval(timerInterval);
            document.getElementById('reservationTimerBar').classList.add('hidden');

            const subtotal = (ticketQty.adult * TICKET_PRICES.adult) + (ticketQty.child * TICKET_PRICES.child);
            const grandTotal = subtotal * 1.05;
            const uuid = 'GF-' + Math.floor(100000 + Math.random() * 900000);

            // Armar objeto Boleto
            const newTicket = {
                id: uuid,
                eventTitle: selectedEvent.title,
                category: selectedEvent.category,
                date: selectedEvent.date,
                location: selectedEvent.location,
                holder: currentUser ? currentUser.name : "Carlos Mendoza",
                adultQty: ticketQty.adult,
                childQty: ticketQty.child,
                totalPaid: grandTotal,
                qrData: `GRADUFEST-VALIDATED:${uuid}:${selectedEvent.id}`
            };

            userPurchases.push(newTicket);

            // Renderizar Vista de Éxito
            document.getElementById('ticketEventTitle').innerText = newTicket.eventTitle;
            document.getElementById('ticketCategory').innerText = newTicket.category;
            document.getElementById('ticketHolder').innerText = newTicket.holder;
            document.getElementById('ticketQtyDetails').innerText = `${newTicket.adultQty} Adulto(s), ${newTicket.childQty} Niño(s)`;
            document.getElementById('ticketUuid').innerText = `ID: ${newTicket.id}`;

            // Generar QR Code
            const qrContainer = document.getElementById("qrcode");
            qrContainer.innerHTML = "";
            new QRCode(qrContainer, {
                text: newTicket.qrData,
                width: 140,
                height: 140,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            navigateTo('success');
        }

        // SIMULACIÓN GOOGLE WALLET
        function addToGoogleWallet() {
            document.getElementById('modalWallet').classList.remove('hidden');
        }

        function closeWalletModal() {
            document.getElementById('modalWallet').classList.add('hidden');
        }

        // RENDERIZAR DASHBOARD "MIS BOLETOS"
        function renderMyTickets() {
            const container = document.getElementById('myTicketsList');
            if (userPurchases.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-12 glass-panel rounded-2xl">
                        <i class="fa-solid fa-ticket text-4xl text-slate-600 mb-3"></i>
                        <p class="text-slate-400 font-medium">Aún no has comprado boletos para ningún evento.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = userPurchases.map(t => `
                <div class="glass-panel rounded-2xl p-5 border border-slate-800 space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[10px] font-bold text-indigo-400 uppercase">${t.category}</span>
                            <h3 class="font-bold text-white text-base">${t.eventTitle}</h3>
                            <p class="text-xs text-slate-400">${t.date}</p>
                        </div>
                        <span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">ACTIVO</span>
                    </div>

                    <div class="p-3 bg-slate-900 rounded-xl flex items-center justify-between text-xs text-slate-300 border border-slate-800">
                        <div>
                            <span class="block text-[10px] text-slate-500">Boletos</span>
                            <strong>${t.adultQty} Adulto(s), ${t.childQty} Niño(s)</strong>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-slate-500">Código Ticket</span>
                            <strong class="font-mono text-amber-400">${t.id}</strong>
                        </div>
                    </div>

                    <button onclick="addToGoogleWallet()" class="w-full py-2 bg-black hover:bg-slate-900 border border-slate-700 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-2">
                        <i class="fa-solid fa-wallet text-amber-400"></i>
                        <span>Ver en Google Wallet</span>
                    </button>
                </div>
            `).join('');
        }
    </script>
</body>
</html>