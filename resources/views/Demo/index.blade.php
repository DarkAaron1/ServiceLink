<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css')}}">
    <title>ServiceLink</title>
</head>

<body>
<script src="{{ asset('index.js') }}"></script>
    <div class="container">
        <!-- Sidebar Section -->
        @include('partials.sidebar')
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Dashboard</h1>
            <!-- Analyses -->
            <div class="analyse">
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Ingresos</h3>
                            <h1>{{ $IngresosTotales }}</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="visits">
                    <div class="status">
                        <div class="info">
                            <h3>Productos Vendidos</h3>
                            <h1>{{ $Solds }}</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Producto Más Vendido</h3>
                            <h1>{{ $nameSoldProduct }}</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">
                <h2>Colaboradores</h2>
                <div class="user-list">
                    @foreach ($empleados as $empleado)
                        <div class="user">
                            <!--img src="images/profile-2.jpg"-->
                            <h2>{{ $empleado->nombre }} {{ $empleado->apellido }}</h2>
                            <p>{{ $empleado->cargo ?? 'N/A' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Ordenes Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Mesa</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 10 más recientes -->
                        @foreach ($comandas as $comanda)
                            <tr>
                                <td>{{ $comanda->mesa->nombre ?? 'Sin Mesas'}}</td>
                                <td>
                                    <ul>
                                        @foreach ($comanda->pedidos as $pedido)
                                            <li>{{ $pedido->item->nombre ?? 'no disponible'}}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $comanda->pedidos->sum('valor_item_ATM') }}</td>
                                <td>{{ ucfirst($comanda->estado) }}</td>
                            </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
             <!-- End of Recent Orders -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        @include('partials.right-section')


    </div>

    <!-- <script src="orders.js"></script> -->
    
</body>

</html>