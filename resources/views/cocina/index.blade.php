<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    @livewireStyles
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

                <!-- Grid de órdenes (Livewire) -->
                <div>
                    @livewire('cocina-orders')
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
                    // notify Livewire to refresh list
                    if (window.livewire) window.livewire.emit('refreshOrders');
                    else location.reload();
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

        // Refrescar órdenes (Livewire)
        document.getElementById('refresh-orders').addEventListener('click', function() {
            if (window.livewire) {
                window.livewire.emit('refreshOrders');
            } else {
                location.reload();
            }
        });


    </script>

    <script>
        // Conectar Echo (Reverb) una vez Livewire esté listo
        function initCocinaRealtimeListener() {
            try {
                if (!window.Echo) {
                    console.warn('Echo no disponible en esta página');
                    return;
                }

                window.Echo.channel('cocina').listen('.NuevoPedidoCreado', function(e) {
                    if (window.livewire && typeof window.livewire.emit === 'function') {
                        window.livewire.emit('refreshOrders');
                    } else {
                        console.log('Nuevo pedido recibido, recargando página');
                        location.reload();
                    }
                });
            } catch (err) {
                console.error('Error al conectar con Echo:', err);
            }
        }

        document.addEventListener('livewire:initialized', initCocinaRealtimeListener);
        document.addEventListener('livewire:load', initCocinaRealtimeListener);
        // Fallback: si Livewire ya está cargado
        if (window.livewire) initCocinaRealtimeListener();
    </script>

    <script>
        // Mostrar tiempo exacto transcurrido desde la creación de la comanda
        // Formato: `MM:SS`, `HH:MM:SS` o `Nd HH:MM:SS` si es necesario.
        function pad(n) { return n.toString().padStart(2, '0'); }

        function elapsedTimeFrom(isoString) {
            const then = new Date(isoString).getTime();
            let diff = Math.max(0, Math.floor((Date.now() - then) / 1000));

            const days = Math.floor(diff / 86400);
            diff %= 86400;
            const hours = Math.floor(diff / 3600);
            diff %= 3600;
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;

            const hhmmss = (hours > 0)
                ? `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
                : `${pad(minutes)}:${pad(seconds)}`;

            return days > 0 ? `${days}d ${hhmmss}` : hhmmss;
        }

        function updateElapsedBadges() {
            document.querySelectorAll('.time-badge').forEach(el => {
                const iso = el.getAttribute('data-created-at');
                if (!iso) return;
                el.textContent = elapsedTimeFrom(iso);
                // Mostrar fecha/hora exacta al pasar el ratón
                if (!el.title) el.title = new Date(iso).toLocaleString();
            });
        }

        // Actualizar inmediatamente y cada segundo para mostrar segundos exactos
        updateElapsedBadges();
        setInterval(updateElapsedBadges, 1000);

        // Recalcular después de cada render de Livewire
        document.addEventListener('livewire:update', updateElapsedBadges);
        document.addEventListener('livewire:load', updateElapsedBadges);
    </script>

    <script>
        // Polling periódica para detectar nuevas órdenes y refrescar la vista
        let cocinaLastSeenIso = null;

        function initCocinaLastSeen() {
            const badges = Array.from(document.querySelectorAll('.time-badge'))
                .map(el => el.getAttribute('data-created-at'))
                .filter(Boolean);
            cocinaLastSeenIso = badges.length ? badges.sort().pop() : null;
        }

        async function checkForNewOrders() {
            try {
                const res = await fetch('/cocina/ordenes/latest', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (data.latest_created_at && (!cocinaLastSeenIso || new Date(data.latest_created_at) > new Date(cocinaLastSeenIso))) {
                    cocinaLastSeenIso = data.latest_created_at;
                    console.log('Nueva orden detectada, refrescando lista de cocina');
                    if (window.livewire && typeof window.livewire.emit === 'function') {
                        window.livewire.emit('refreshOrders');
                    } else {
                        location.reload();
                    }
                }
            } catch (err) {
                console.warn('Error comprobando nuevas órdenes:', err);
            }
        }

        // Inicializar y arrancar polling
        initCocinaLastSeen();
        // Re-check on focus to be more responsive
        window.addEventListener('focus', checkForNewOrders);
        // Poll cada 15 segundos
        setInterval(checkForNewOrders, 15 * 1000);

        // Actualizar el punto de referencia cuando Livewire renderice (evita refrescos duplicados)
        document.addEventListener('livewire:update', initCocinaLastSeen);
        document.addEventListener('livewire:load', initCocinaLastSeen);

        // Hacer una comprobación inicial
        checkForNewOrders();
    </script>

    @livewireScripts
</body>

</html>