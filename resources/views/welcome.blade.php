<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Contenido Principal Centrado -->
        <div class="flex flex-col items-center justify-center min-h-screen p-4">
            <div class="w-full max-w-xl bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/r.jpg') }}"
                             alt="Logo"
                             class="w-96 h-96 object-cover rounded-lg shadow-md">
                    </div>

                    <div class="mt-6 text-center">
                        <h1 class="text-2xl font-bold text-indigo-600">
                            Sistema de Gestión
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Bienvenido a tu plataforma integral de gestión empresarial
                        </p>

                        <!-- Botón de inicio de sesión -->
                        @if (Route::has('login'))
                            <div class="mt-6">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                       style="padding:15px">
                                        Panel de Control
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                       style="padding:15px">
                                        Iniciar Sesión
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
