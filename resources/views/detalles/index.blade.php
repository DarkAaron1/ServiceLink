<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink - Comandas</title>
    <!-- Los estilos de mesas se cargan desde style-tables.css -->
    <style>
        /* Modal: no scroll propio, el scroll estará sólo en el listado de items */
        .mesa-modal {
            padding: 2rem 0;
        }

        .modal-content {
            margin: auto;
            max-height: 90vh;
        }
        /* Asegurar que la lista de items tenga scroll independiente */
        #items-list-wrapper { overflow:auto; max-height:350px; }
    </style>
</head>
<body>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Comanda #{{ $comanda->id }}
        </h1>
        <div class="flex items-center space-x-3">
            {{-- Indicador de Estado Actual --}}
            @php
                $statusColor = [
                    'abierta' => 'bg-green-100 text-green-800',
                    'cerrada' => 'bg-gray-100 text-gray-800',
                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                    'en_preparacion' => 'bg-blue-100 text-blue-800',
                ];
                $estadoClase = $statusColor[strtolower($comanda->estado_cuenta ?? 'pendiente')] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $estadoClase }}">
                Estado: {{ ucfirst($comanda->estado_cuenta ?? 'Pendiente') }}
            </span>
            
            {{-- Botón para Volver --}}
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-150">
                &larr; Volver
            </a>
        </div>
    </div>

    {{-- --- GRID PRINCIPAL DE DETALLE --- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLUMNA 1: DETALLE DE LOS PEDIDOS (66% en pantalla grande) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">
                Items de la Orden
            </h2>
            
            {{-- Encabezado de la Tabla --}}
            <div class="grid grid-cols-12 gap-4 text-xs font-semibold text-gray-500 uppercase pb-2 border-b">
                <div class="col-span-1">Cant.</div>
                <div class="col-span-6">Producto / Detalle</div>
                <div class="col-span-2 text-right">Precio Unitario</div>
                <div class="col-span-3 text-right">Subtotal</div>
            </div>

            {{-- Lista de Pedidos --}}
            @php $totalCuenta = 0; @endphp
            @forelse ($comanda->pedidos as $pedido)
                <div class="grid grid-cols-12 gap-4 items-center py-3 border-b hover:bg-gray-50 transition duration-100">
                    
                    {{-- Cantidad (usando la agrupación de pedidos anterior, si no se agrupan en el controller, usa 1) --}}
                    <div class="col-span-1 text-sm font-medium text-gray-900">1</div> 
                    
                    {{-- Producto y Observaciones --}}
                    <div class="col-span-6">
                        <p class="text-sm font-medium text-gray-900">{{ $pedido->item_nombre ?? 'Ítem no disponible' }}</p>
                        @if ($pedido->observaciones)
                            <p class="text-xs text-red-500 italic">Obs: {{ $pedido->observaciones }}</p>
                        @endif
                        {{-- Estado de Preparación (para el empleado) --}}
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $pedido->estado_preparacion === 'listo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($pedido->estado_preparacion ?? 'pendiente') }}
                        </span>
                    </div>
                    
                    {{-- Precio Unitario --}}
                    <div class="col-span-2 text-right text-sm text-gray-700">
                        ${{ number_format($pedido->valor_item_ATM ?? 0, 0, ',', '.') }}
                    </div>

                    {{-- Subtotal --}}
                    @php 
                        $subtotal = $pedido->valor_item_ATM ?? 0;
                        $totalCuenta += $subtotal;
                    @endphp
                    <div class="col-span-3 text-right text-base font-semibold text-gray-900">
                        ${{ number_format($subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500 italic py-4">Esta comanda no tiene ítems registrados.</p>
            @endforelse

            {{-- Observaciones Globales de la Comanda --}}
            @if ($comanda->observaciones_global)
                <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-md">
                    <p class="text-sm font-semibold text-red-700">Nota de la Comanda:</p>
                    <p class="text-sm text-red-600">{{ $comanda->observaciones_global }}</p>
                </div>
            @endif
        </div> {{-- Fin Columna 1 --}}

        
        {{-- COLUMNA 2: RESUMEN DE LA CUENTA Y PAGO (33% en pantalla grande) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 h-fit">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">
                Resumen y Pago
            </h2>

            {{-- Info de la Mesa y Mesero --}}
            <div class="mb-6 space-y-1">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Mesa:</span> {{ $comanda->mesa->nombre ?? 'N/A' }}
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Mesero/a:</span> {{ $comanda->empleado->nombre ?? 'N/A' }}
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Creada:</span> {{ $comanda->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            {{-- Resumen de Totales --}}
            <div class="space-y-3 pt-4 border-t">
                <div class="flex justify-between text-base">
                    <span>Subtotal</span>
                    <span class="font-medium">${{ number_format($totalCuenta, 0, ',', '.') }}</span>
                </div>
                
                {{-- Aquí puedes agregar descuentos, propinas, impuestos, si aplican --}}
                <div class="flex justify-between text-base">
                    <span>Propina Sugerida (10%)</span>
                    <span class="font-medium text-blue-600">${{ number_format($totalCuenta * 0.1, 0, ',', '.') }}</span>
                </div>

                {{-- TOTAL FINAL --}}
                <div class="flex justify-between text-2xl font-bold pt-2 border-t-2 border-gray-300">
                    <span>TOTAL A PAGAR</span>
                    <span class="text-blue-600">${{ number_format($totalCuenta * 1.1, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- --- ACCIONES DE PAGO --- --}}
            <div class="mt-8 space-y-4">
                
                @if (strtolower($comanda->estado_cuenta) === 'abierta')
                    
                    {{-- Botón de Pagar (Simulación) --}}
                    <button type="button" 
                        class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition duration-150 shadow-md transform hover:scale-[1.01]">
                        Procesar Pago (Marcar como Pagada)
                    </button>
                    
                    {{-- Botón para Imprimir (si tienes esa funcionalidad) --}}
                    <button type="button" 
                        onclick="window.print()"
                        class="w-full py-3 border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-lg transition duration-150">
                        Imprimir Cuenta
                    </button>
                @else
                    <p class="text-center text-lg text-gray-500 font-semibold pt-4">
                        ✅ Esta comanda ya fue pagada.
                    </p>
                @endif
                
                {{-- Aquí podrías integrar un componente Livewire para el proceso de pago --}}
                {{-- @livewire('payment-processor', ['comanda' => $comanda]) --}}
                
            </div>

        </div> {{-- Fin Columna 2 --}}

    </div> {{-- Fin Grid Principal --}}

</div>

@push('scripts')
<script>
    // Script adicional para manejar la lógica de pago con AJAX/Livewire si fuera necesario
</script>
@endpush