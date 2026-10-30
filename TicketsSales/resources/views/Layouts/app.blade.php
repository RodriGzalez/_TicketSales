<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GraduFest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Tu Navbar personalizado -->
    @include('layouts.navigation') <!-- O tu navbar actual -->

    <main>
        @isset($slot)
            {{ $slot }}
        @endisset

        @yield('content')
    </main>

</body>
</html>