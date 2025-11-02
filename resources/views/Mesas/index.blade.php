<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink</title>
<style>
    
</style>
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
                <a href="{{ url('/newhome') }}">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Colaboradores</h3>
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
                <a href="#">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Comandas</h3>
                    <span class="message-count">27</span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Menú</h3>
                </a>
                <a href="{{ route('Mesas.index') }}" class="active">
                    <span class="material-icons-sharp">
                        table_restaurant
                    </span>
                    <h3>Mesas</h3>
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
            <h1>Gestión de Mesas</h1>
            <!-- Gestión de Mesas -->
            <div style="margin-bottom:2rem;" class="management-tables">
                <div class="header" style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                    <button id="new-mesa-btn" class="btn-primary button-Add">
                        <span class="material-icons-sharp" style="font-size:1.3rem;">add</span>
                        Nueva Mesa
                    </button>
                </div>

                <div class="mesas-grid" style="display:flex; flex-wrap:wrap; gap:1rem; margin-top:1rem;">
                    @if (isset($mesas) && $mesas->count())
                        @foreach ($mesas as $mesa)
                            <div class="mesa-card notification" data-id="{{ $mesa->id }}"
                                style="border:1px solid #e9eef6; padding:1rem; border-radius:8px; background:#fafcff; min-width:220px; flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                                <div class="icon">
                                    <span class="material-icons-sharp"
                                        style="font-size:28px; color:#1976d2;">table_restaurant</span>
                                </div>
                                <div class="content" style="flex:1;">
                                    <div class="info"
                                        style="display:flex; align-items:center; justify-content:space-between;">
                                        <h3 style="margin:0; font-size:1.05rem;">{{ $mesa->nombre }}</h3>
                                        <span class="badge-estado"
                                            style="padding:.25rem .5rem; border-radius:999px; font-size:.8rem; font-weight:600;">
                                            {{ $mesa->estado }}
                                        </span>
                                    </div>
                                    <p style="margin:.5rem 0 0 0; color:#666; font-size:.9rem;">ID:
                                        {{ $mesa->id }}</p>
                                </div>
                                <div style="display:flex; gap:.5rem; margin-top:1rem;">
                                    <button class="select-mesa-btn"
                                        style="flex:1; padding:.5rem; border-radius:6px; border:1px solid #1976d2; background:#fff; color:#1976d2;">Seleccionar</button>
                                    <form method="POST" action="{{ url('/mesas/' . $mesa->id) }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            style="background:#f44336;color:#fff;border:none;padding:.45rem .6rem;border-radius:6px;">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No hay mesas registradas.</p>
                    @endif
                </div>
            </div>

            <!-- Modal para nueva mesa -->
            <div>
                <div id="mesa-modal" class="mesa-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                    <div class="modal-content">
                        <button id="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                        <div class="modal-header">
                            <span class="modal-icon material-icons-sharp">table_restaurant</span>
                            <h2>Nueva Mesa</h2>
                        </div>
                        <form id="mesa-form" method="POST" action="{{ url('/mesas') }}">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="nombre">Nombre de la mesa</label>
                                    <input id="nombre" name="nombre" required placeholder="Ej: Mesa 1" autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <label for="estado">Estado</label>
                                    <select id="estado" name="estado" required class="estado">
                                        <option value="Disponible" >Disponible</option>
                                        <option value="Ocupada">Ocupada</option>
                                        <option value="Reservada">Reservada</option>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="cancel-mesa">Cancelar</button>
                                    <button type="submit">Crear Mesa</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                // JS para modal y selección de mesas
                (function() {
                    const newBtn = document.getElementById('new-mesa-btn');
                    const modal = document.getElementById('mesa-modal');
                    const closeModal = document.getElementById('close-modal');
                    const cancelMesa = document.getElementById('cancel-mesa');

                    function openModal() {
                        if (!modal) return;
                        modal.style.display = 'flex';
                        modal.setAttribute('aria-hidden', 'false');
                        // focus primer campo
                        const firstInput = modal.querySelector('#nombre');
                        if (firstInput) firstInput.focus();
                    }

                    function hideModal() {
                        if (!modal) return;
                        modal.style.display = 'none';
                        modal.setAttribute('aria-hidden', 'true');
                        // devolver foco al botón que abre el modal
                        if (newBtn) newBtn.focus();
                    }

                    if (newBtn) newBtn.addEventListener('click', openModal);
                    if (closeModal) closeModal.addEventListener('click', hideModal);
                    if (cancelMesa) cancelMesa.addEventListener('click', hideModal);

                    // Cerrar al hacer click fuera del contenido
                    modal && modal.addEventListener('click', function(e) {
                        if (e.target === modal) hideModal();
                    });

                    // Seleccionar mesa (cliente-side toggle). Puedes extender para enviar al servidor.
                    document.querySelectorAll('.select-mesa-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const card = this.closest('.mesa-card');
                            if (!card) return;
                            // toggle visual
                            card.classList.toggle('selected');
                            if (card.classList.contains('selected')) {
                                card.style.background = '#e3f2fd';
                                this.textContent = 'Seleccionada';
                            } else {
                                card.style.background = '';
                                this.textContent = 'Seleccionar';
                            }
                        });
                    });

                    // Intercepción opcional del submit para agregar la nueva mesa al DOM sin recargar
                    const form = document.getElementById('mesa-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            // Dejar que el formulario haga la petición al servidor por defecto.
                            // Si quieres manejo AJAX, descomenta lo siguiente y añade la lógica fetch.
                            // e.preventDefault();
                            // TODO: enviar por fetch y al completar añadir tarjeta a .mesas-grid
                        });
                    }
                })();
            </script>

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

    <!-- <script src="orders.js"></script> -->
    <script src="{{ asset('index.js') }}"></script>
</body>

</html>
