<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink - Cocina</title>
</head>

<body>

    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="{{ asset('favicon.ico') }}">
                    <h2>Service<span class="primary">Link</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="{{ route('demo.index') }}">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="{{ route('empleados.index') }}">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Colaboradores</h3>
                </a>
                <a href="{{ route('comandas.index') }}">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>Comandas</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">insights</span>
                    <h3>Estadísticas</h3>
                </a>
                <a href="{{ route('cocina.index') }}" class="active">
                    <span class="material-icons-sharp">restaurant</span>
                    <h3>Cocina</h3>
                </a>
                <a href="{{ route('items_menu.index') }}">
                    <span class="material-icons-sharp">inventory</span>
                    <h3>Menú</h3>
                </a>
                <a href="{{ route('mesas.index') }}">
                    <span class="material-icons-sharp">table_restaurant</span>
                    <h3>Mesas</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Gestión de Cocina</h1>

            @if (session('success'))
                <div class="alert alert-success"
                    style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="management-tables" style="margin-bottom:2rem;">
                <div class="header" style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div>
                        <h3>Órdenes de Cocina</h3>
                        <p>Gestiona los pedidos pendientes de preparación</p>
                    </div>
                    <div style="display:flex; gap:0.75rem; align-items:center;">
                        <button id="refresh-orders" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <span class="material-icons-sharp">refresh</span>
                            Refrescar
                        </button>
                    </div>
                </div>

                <!-- Filtro por estado -->
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                    <label for="filter_estado" style="font-weight:500;">Filtrar por estado:</label>
                    <select id="filter_estado"
                        style="padding:0.5rem; border-radius:8px; border:1px solid #e2e8f0; background:#fff;">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_preparacion">En Preparación</option>
                        <option value="listo">Listo</option>
                    </select>
                </div>

                <!-- Grid de órdenes -->
                <div id="orders-grid" class="menu-grid">
                    @if (isset($comandas) && $comandas->count())
                        @foreach ($comandas as $comanda)
                            <div class="order-card estado-{{ strtolower($comanda->estado) }}" 
                                data-order-id="{{ $comanda->id }}" data-estado="{{ $comanda->estado }}">
                                <div class="card-content">
                                    <div class="card-header">
                                        <h3>Mesa {{ $comanda->mesa->nombre ?? 'N/A' }}</h3>
                                        <span class="time-badge">{{ $comanda->created_at->diffForHumans() }}</span>
                                    </div>

                                    <div class="order-items" style="margin:1rem 0; max-height:200px; overflow-y:auto;">
                                        @forelse ($comanda->detalles as $detalle)
                                            <div class="order-item"
                                                style="padding:0.75rem; border-left:3px solid #3b82f6; margin-bottom:0.5rem; background:#f8fafc;">
                                                <div style="display:flex; justify-content:space-between; align-items:start;">
                                                    <div>
                                                        <strong>{{ $detalle->item->nombre ?? 'Item' }}</strong>
                                                        <p style="font-size:0.9rem; color:#64748b; margin:0.25rem 0 0 0;">
                                                            Cantidad: {{ $detalle->cantidad }}
                                                        </p>
                                                        @if ($detalle->observaciones)
                                                            <p style="font-size:0.85rem; color:#f59e0b; margin:0.25rem 0 0 0; font-weight:500;">
                                                                📝 {{ $detalle->observaciones }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span class="item-status"
                                                        style="padding:0.3rem 0.6rem; border-radius:4px; font-size:0.8rem; background:#e2e8f0;">
                                                        {{ $detalle->estado ?? 'pendiente' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <p style="color:#94a3b8;">Sin detalles</p>
                                        @endforelse
                                    </div>

                                    @if ($comanda->observaciones_global)
                                        <div style="background:#fef3c7; padding:0.75rem; border-radius:6px; margin:0.75rem 0; border-left:3px solid #f59e0b;">
                                            <p style="font-size:0.9rem; margin:0; color:#92400e;">
                                                <strong>Nota:</strong> {{ $comanda->observaciones_global }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="card-footer"
                                        style="display:flex; justify-content:space-between; align-items:center; margin-top:1rem;">
                                        <span class="status estado-{{ strtolower($comanda->estado) }}"
                                            style="padding:0.4rem 0.8rem; border-radius:6px;">
                                            {{ ucfirst($comanda->estado) }}
                                        </span>
                                    </div>

                                    <div class="card-actions" style="margin-top:1rem; display:flex; gap:0.5rem;">
                                        @if ($comanda->estado !== 'en_preparacion')
                                            <button class="btn-action" onclick="updateOrderStatus({{ $comanda->id }}, 'en_preparacion')"
                                                style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#3b82f6; color:#fff; cursor:pointer;">
                                                <span class="material-icons-sharp" style="font-size:1rem;">schedule</span>
                                                En Preparación
                                            </button>
                                        @endif

                                        @if ($comanda->estado === 'en_preparacion')
                                            <button class="btn-action" onclick="updateOrderStatus({{ $comanda->id }}, 'listo')"
                                                style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#10b981; color:#fff; cursor:pointer;">
                                                <span class="material-icons-sharp" style="font-size:1rem;">check_circle</span>
                                                Marcar Listo
                                            </button>
                                        @endif

                                        <button class="btn-action" onclick="viewOrderDetails({{ $comanda->id }})"
                                            style="flex:1; padding:0.6rem; border:none; border-radius:6px; background:#8b5cf6; color:#fff; cursor:pointer;">
                                            <span class="material-icons-sharp" style="font-size:1rem;">visibility</span>
                                            Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="grid-column:1/-1; text-align:center; padding:2rem;">
                            <span class="material-icons-sharp" style="font-size:3rem; color:#cbd5e1;">inbox</span>
                            <p style="color:#64748b; margin-top:1rem;">No hay órdenes pendientes</p>
                        </div>
                    @endif
                </div>
            </div>

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Bienvenido, <b>{{ $usuario->nombre ?? 'Usuario' }}</b></p>
                        <small class="text-muted">{{ $rolName ?? 'Cocinero' }}</small>
                    </div>
                </div>
            </div>

            <div class="user-profile">
                <div class="logo">
                    <img src="{{ asset('favicon.ico') }}">
                    <h2>{{ $usuario->nombre ?? 'Usuario' }}</h2>
                    <p>{{ $rolName ?? 'Rol' }}</p>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('index.js') }}"></script>
    <script>
        // Actualizar estado de orden
        function updateOrderStatus(orderId, newStatus) {
            if (!confirm(`¿Cambiar estado a ${newStatus}?`)) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/cocina/ordenes/${orderId}/estado`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado');
            });
        }

        // Ver detalles de orden
        function viewOrderDetails(orderId) {
            alert(`Detalles de orden ${orderId}`);
            // Implementar modal con detalles si es necesario
        }

        // Filtrar por estado
        document.getElementById('filter_estado').addEventListener('change', function() {
            const estado = this.value;
            const cards = document.querySelectorAll('.order-card');
            
            cards.forEach(card => {
                if (estado === 'todos' || card.dataset.estado === estado) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Refrescar órdenes
        document.getElementById('refresh-orders').addEventListener('click', function() {
            location.reload();
        });

        // Auto-refresh cada 30 segundos
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>

</body>

</html>