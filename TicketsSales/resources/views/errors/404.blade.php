<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - GraduFest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen font-sans">
    <div class="text-center px-4 max-w-md">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 mb-4 text-2xl font-bold">
            404
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Vaya! Página no encontrada</h1>
        <p class="text-gray-500 text-sm mb-6">La página que buscas no existe, fue movida o el enlace es incorrecto.</p>
        <a href="{{ route('events.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-xl transition text-sm shadow-sm">
            Volver al Inicio
        </a>
    </div>
</body>
</html>