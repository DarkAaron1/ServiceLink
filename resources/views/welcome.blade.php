<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet"/>
    <title>ServiceLink | Sistema de Gestión de Servicios</title>

    @vite('resources/css/app.css')

    <style>
        /* Estilos personalizados para un mejor efecto visual */
        .hero-bg {
            background-color: #f7f9fb; /* Fondo claro para la sección principal */
            background-image: radial-gradient(circle at 100% 100%, #e6f0ff 0%, transparent 70%);
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <header class="p-6 bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo -->
            <img src="{{ asset('favicon.ico') }}" alt="ServiceLink Logo" class="h-20">
            <nav class="space-x-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-600 hover:text-blue-600 font-semibold transition duration-150">Panel de Control</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600 font-semibold transition duration-150">Acceso Clientes</a>
                        
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-white bg-blue-600 hover:bg-blue-700 py-2 px-4 rounded-lg font-semibold transition duration-150 shadow-md">
                                Registrar Restaurante
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main class="hero-bg pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-12 text-center">
            <h2 class="text-6xl font-extrabold leading-tight text-gray-900 mb-6">
                Simplifica la Gestión de Pedidos y Servicios
            </h2>
            <p class="text-xl text-gray-500 max-w-3xl mx-auto mb-10">
                ServiceLink es tu solución integral para digitalizar la operación de tu restaurante: desde la toma de comandas hasta la visualización en cocina, todo en tiempo real.
            </p>
            
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="inline-block text-xl text-white bg-blue-600 hover:bg-blue-700 py-4 px-8 rounded-xl font-bold transition duration-300 shadow-lg transform hover:scale-105">
                    Comienza Gratis Hoy
                </a>
                <a href="#features" class="inline-block text-xl text-blue-600 border-2 border-blue-600 hover:bg-blue-50 py-4 px-8 rounded-xl font-bold transition duration-300">
                    Ver Características
                </a>
            </div>
        </div>
    </main>
    
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-sm font-semibold uppercase text-blue-600 tracking-wider">Módulos Clave</h3>
                <h2 class="text-4xl font-bold text-gray-900 mt-2">Diseñado para la Eficiencia Operacional</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                
                <div class="p-8 bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-4xl text-blue-500 mb-4">
                        <span class="material-icons-sharp">list_alt</span>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Toma de Comandas Digital</h4>
                    <p class="text-gray-600">
                        Los meseros pueden ingresar pedidos directamente desde tablets o móviles, minimizando errores y acelerando el proceso de atención al cliente.
                    </p>
                </div>
                
                <div class="p-8 bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-4xl text-green-500 mb-4">
                        <span class="material-icons-sharp">restaurant_menu</span>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Display de Cocina (KDS)</h4>
                    <p class="text-gray-600">
                        Visualiza órdenes pendientes, en preparación y listas en un panel intuitivo que se actualiza automáticamente gracias a la tecnología de tiempo real.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="text-4xl text-purple-500 mb-4">
                        <span class="material-icons-sharp">people_alt</span>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Control de Flujo de Trabajo</h4>
                    <p class="text-gray-600">
                        Monitorea el estado de cada mesa, asigna personal y gestiona roles (administrador, empleado, cocina) de forma centralizada.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-blue-600 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">
                ¿Listo para Digitalizar tu Restaurante?
            </h2>
            <p class="text-xl text-blue-200 mb-8">
                Únete a la plataforma que está redefiniendo la eficiencia en el sector gastronómico.
            </p>
            <a href="{{ route('register') }}" class="inline-block text-xl text-blue-600 bg-white hover:bg-gray-200 py-4 px-10 rounded-xl font-bold transition duration-300 shadow-xl transform hover:scale-105">
                Empezar Ahora
            </a>
        </div>
    </section>

    <footer class="py-10 bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} ServiceLink. Todos los derechos reservados.</p>
            <div class="mt-4 space-x-4 text-sm">
                <a href="#" class="text-gray-400 hover:text-blue-400">Política de Privacidad</a>
                <a href="#" class="text-gray-400 hover:text-blue-400">Términos de Servicio</a>
            </div>
        </div>
    </footer>

    @livewireScripts 
</body>
</html>