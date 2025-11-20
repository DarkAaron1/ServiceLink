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

    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="{{ asset('favicon.ico') }}">
                    <h2>Service<span class="primary">Link</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="#" class="active">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="{{ route('usuarios.index') }}">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Empleados</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>Ventas</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Estadísticas</h3>
                </a>
                <a href="{{ route('comandas.index', ['open' => 'true']) }}">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Comandas</h3>
                    <span class="message-count">{{ count($comandas ?? []) }}</span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Menú</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        table_restaurant
                    </span>
                    <h3>Mesas</h3>
                </a>
               <a href="{{ route('usuarios.index') }}">
                <span class="material-icons-sharp">
               group
                </span>
                <h3>Usuarios</h3>
                </a>


                </a>
                <!--<a href="#">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>New Login</h3>
                </a>-->
                <a href="#">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Comandas</h1>

            <!-- Modal overlay (hidden) -->
            <div id="modal-overlay" style="display:none; position: fixed; inset:0; background: rgba(0,0,0,0.45); z-index: 9999; align-items: center; justify-content: center;">
                <div id="modal" role="dialog" aria-modal="true" style="background: #fff; width: 90%; max-width: 820px; margin: 0 auto; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); overflow: hidden;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #eee; background:#fafafa;">
                        <h3 id="modal-title" style="margin:0;">Crear Nueva Comanda</h3>
                        <button id="close-modal" aria-label="Cerrar" style="background:transparent; border:none; font-size:18px; cursor:pointer;">✖</button>
                    </div>
                    <div id="modal-content" style="padding:16px;">
                        @include('Comandas._form', ['action' => route('comandas.store'), 'method' => 'POST', 'comanda' => null, 'inModal' => true, 'empleados' => $empleados, 'mesas' => $mesas])
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div style="background:#d4edda; border:1px solid #c3e6cb; padding:10px; border-radius:4px; margin-bottom:12px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Comandas Table Section -->
            <div class="recent-orders">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Listado de Comandas</h2>
                    <a href="#" id="new-comanda-btn" style="background-color: #007bff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px; cursor: pointer;">+ Nueva Comanda</a>
                </div>

                @if(isset($comandas) && $comandas->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Empleado(RUT)</th>
                                <th>Mesa</th>
                                <th>Estado</th>
                                <th>Fecha Apertura</th>
                                <th>Fecha Cierre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comandas as $comanda)
                                <tr>
                                    <td>{{ $comanda->id }}</td>
                                    <td>{{ $comanda->rut_empleado }}</td>
                                    <td>{{ optional($comanda->mesa)->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; {{ $comanda->estado === 'abierta' ? 'background:#28a745; color:white;' : 'background:#dc3545; color:white;' }}">
                                            {{ ucfirst($comanda->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $comanda->fecha_apertura ? $comanda->fecha_apertura->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $comanda->fecha_cierre ? $comanda->fecha_cierre->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="edit-comanda-btn" data-id="{{ $comanda->id }}" style="background-color: #ffc107; color: white; padding: 6px 12px; border-radius: 4px; border: none; font-weight: bold; font-size: 12px; cursor: pointer;">✏️ Editar</button>
                                        <form action="{{ route('comandas.destroy', $comanda->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 12px; cursor: pointer;" onclick="return confirm('¿Estás seguro?');">🗑️ Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="background:#e9ecef; padding:20px; border-radius:4px; text-align:center;">
                        <p>No hay comandas registradas.</p>
                    </div>
                @endif
            </div>
            <!-- End of Comandas Table Section -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                        <div class="info">
                            <p>Bienvenido, <b>Usuario</b></p>
                            <small class="text-muted">Admin</small>
                        </div>
                        <!--div class="profile-photo">
                            <img src="{{ asset('favicon.ico') }}">
                        </div-->
                    </div>

            </div>
            <!-- End of Nav -->

            <div class="user-profile">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>ServiceLink</h2>
                    <p>Dueño de Restaurante</p>
                </div>
            </div>

            <div class="reminders">
                <div class="header">
                    <h2>Notificaciones</h2>
                    <span class="material-icons-sharp">
                        notifications_none
                    </span>
                </div>

                <div class="notification">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            volume_up
                        </span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">
                                08:00 AM - 12:00 PM
                            </small>
                        </div>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>

                <div class="notification deactive">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            edit
                        </span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">
                                08:00 AM - 12:00 PM
                            </small>
                        </div>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>

                <div class="notification add-reminder">
                    <div>
                        <span class="material-icons-sharp">
                            add
                        </span>
                        <h3>Add Reminder</h3>
                    </div>
                </div>

            </div>

        </div>


    </div>

    <script>
        // Funciones para el modal de Comandas
        function openModal() {
            document.getElementById('modal-overlay').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal-overlay').style.display = 'none';
        }

        // Funciones para manejar event listeners del modal
        function setupModalListeners() {
            const closeBtn = document.getElementById('close-modal');
            const cancelBtn = document.getElementById('cancel-new-comanda');
            const modalOverlay = document.getElementById('modal-overlay');
            
            if (closeBtn) {
                closeBtn.onclick = closeModal;
            }
            
            if (cancelBtn) {
                cancelBtn.onclick = closeModal;
            }
            
            if (modalOverlay) {
                modalOverlay.onclick = (e) => {
                    if (e.target.id === 'modal-overlay') {
                        closeModal();
                    }
                };
            }
        }

        // Event listener para el botón de crear nueva comanda
        document.getElementById('new-comanda-btn')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('modal-title').textContent = 'Crear Nueva Comanda';
            openModal();
            setupModalListeners();
        });

        // Event listeners para editar
        function setupEditListeners() {
            document.querySelectorAll('.edit-comanda-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const comandaId = btn.dataset.id;
                    
                    try {
                        const response = await fetch(`/comandas/${comandaId}/form`);
                        const html = await response.text();
                        
                        document.getElementById('modal-title').textContent = 'Editar Comanda';
                        document.getElementById('modal-content').innerHTML = html;
                        setupModalListeners();
                        openModal();
                    } catch (error) {
                        console.error('Error al cargar el formulario de edición:', error);
                        alert('Error al cargar el formulario: ' + error.message);
                    }
                });
            });
        }

        // Inicializar event listeners
        setupEditListeners();
        setupModalListeners();

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Auto-abrir modal si hay errores
        @if ($errors->any())
            openModal();
        @endif

        // Auto-abrir modal si la query string contiene open=true
        try {
            const params = new URLSearchParams(window.location.search);
            if (params.get('open') === 'true') {
                openModal();
            }
        } catch (e) {
            // URLSearchParams no disponible o error; no hacemos nada
        }
    </script>

    <!-- <script src="orders.js"></script> -->
    <script src="{{ asset('index.js') }}"></script>
</body>

</html>