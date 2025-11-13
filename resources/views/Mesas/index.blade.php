<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink - Mesas</title>
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
                <a href="{{ route('mesas.index') }}" class="active">
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

                <div class="mesas-grid">
                    @if (isset($mesas) && $mesas->count())
                        @foreach ($mesas as $mesa)
                            <div class="mesa-card estado-{{ strtolower($mesa->estado) }}" data-id="{{ $mesa->id }}">
                                <div class="icon">
                                    <span class="material-icons-sharp">table_restaurant</span>
                                </div>
                                <h3>{{ $mesa->nombre }}</h3>
                                <div class="estado-container">
                                    <div class="estado-indicador">
                                        {{ $mesa->estado }}
                                    </div>
                                </div>
                                <div class="mesa-actions">
                                    <button class="mesa-btn edit"
                                        onclick="openEditModal({{ $mesa->id }}, '{{ $mesa->nombre }}', '{{ $mesa->estado }}')">
                                        <span class="material-icons-sharp">edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('mesas.destroy', $mesa->id) }}"
                                        style="flex:1; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mesa-btn delete"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta mesa?')">
                                            <span class="material-icons-sharp">delete</span>
                                        </button>
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
                <div id="mesa-modal" class="mesa-modal" role="dialog" aria-modal="true" aria-hidden="true"
                    tabindex="-1">
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
                                    <input id="nombre" name="nombre" required placeholder="Ej: Mesa 1"
                                        autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <label for="estado">Estado</label>
                                    <select id="estado" name="estado" required class="estado">
                                        <option value="Disponible">Disponible</option>
                                        <option value="Ocupada">Ocupada</option>
                                        <option value="Reservada">Reservada</option>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="cancel-mesa">Cancelar</button>
                                    <button type="submit" id="crear-mesa-btn" class="submit-btn"> Crear Mesa
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para editar mesa -->
            <div>
                <div id="editar-mesa-modal" class="mesa-modal" role="dialog" aria-modal="true" aria-hidden="true"
                    tabindex="-1">
                    <div class="modal-content">
                        <button id="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                        <div class="modal-header">
                            <span class="modal-icon material-icons-sharp">table_restaurant</span>
                            <h2>Editar Mesa</h2>
                        </div>
                        <form id="editar-mesa-form" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="edit-nombre">Nombre de la mesa</label>
                                    <input id="edit-nombre" name="nombre" required placeholder="Ej: Mesa 1"
                                        autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <label for="edit-estado">Estado</label>
                                    <select id="edit-estado" name="estado" required class="estado">
                                        <option value="Disponible">Disponible</option>
                                        <option value="Ocupada">Ocupada</option>
                                        <option value="Reservada">Reservada</option>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="cancel-edit-mesa">Cancelar</button>
                                    <button type="submit" id="editar-mesa-btn" class="submit-btn">
                                        Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Fin  Modal para crear mesa -->

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

            // Referencias a elementos del modal de editar
            const editModal = document.getElementById('editar-mesa-modal');
            const editCloseBtn = editModal.querySelector('#close-modal');
            const cancelEditBtn = document.getElementById('cancel-edit-mesa');
            const editForm = document.getElementById('editar-mesa-form');
            const editNombreInput = document.getElementById('edit-nombre');
            const editEstadoSelect = document.getElementById('edit-estado');

            // Funciones para el modal de editar
            window.openEditModal = function(id, nombre, estado) {
                if (!editModal) return;

                // Configurar el formulario
                editForm.action = `/mesas/${id}`;
                editNombreInput.value = nombre;
                editEstadoSelect.value = estado;

                // Mostrar el modal
                editModal.style.display = 'flex';
                editModal.setAttribute('aria-hidden', 'false');
                editNombreInput.focus();

                // Guardar los valores originales para comparar cambios
                editForm.dataset.originalNombre = nombre;
                editForm.dataset.originalEstado = estado;
            }

            function hideEditModal() {
                if (!editModal) return;
                editModal.style.display = 'none';
                editModal.setAttribute('aria-hidden', 'true');
                if (editForm) editForm.reset();
            }

            // Event listeners para el modal de editar
            editCloseBtn.addEventListener('click', hideEditModal);
            cancelEditBtn.addEventListener('click', hideEditModal);
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) hideEditModal();
            });

            // Cerrar al hacer click fuera del contenido
            modal && modal.addEventListener('click', function(e) {
                if (e.target === modal) hideModal();
            });

            // Manejar cambios en los campos del formulario de edición
            editNombreInput.addEventListener('input', validateChanges);
            editEstadoSelect.addEventListener('change', validateChanges);

            function validateChanges() {
                const submitBtn = document.getElementById('editar-mesa-btn');
                const hasChanges =
                    editNombreInput.value !== editForm.dataset.originalNombre ||
                    editEstadoSelect.value !== editForm.dataset.originalEstado;

                submitBtn.disabled = !hasChanges;
            }

            // Manejar el envío del formulario de edición
            if (editForm) {
                editForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('editar-mesa-btn');

                    try {
                        submitBtn.disabled = true;
                        const formData = new FormData(editForm);
                        formData.append('_method', 'PATCH');
                        const mesaId = editForm.action.split('/').pop();

                        const response = await fetch(`/mesas/${mesaId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const error = await response.json();
                            throw new Error(error.message || 'Error al actualizar la mesa');
                        }

                        const data = await response.json();

                        if (data.success) {
                            // Actualizar la tarjeta en la interfaz
                            const mesaCard = document.querySelector(`.mesa-card[data-id="${mesaId}"]`);
                            if (mesaCard) {
                                mesaCard.querySelector('h3').textContent = formData.get('nombre');
                                mesaCard.className =
                                    `mesa-card estado-${formData.get('estado').toLowerCase()}`;
                                mesaCard.querySelector('.estado-indicador').textContent = formData.get(
                                    'estado');

                                // Actualizar el botón de editar con los nuevos datos
                                const editBtn = mesaCard.querySelector('.edit');
                                editBtn.setAttribute('onclick',
                                    `openEditModal(${mesaId}, '${formData.get('nombre')}', '${formData.get('estado')}')`
                                    );
                            }

                            // Cerrar modal y mostrar mensaje de éxito
                            hideEditModal();

                        } else {
                            throw new Error(data.message || 'Error al actualizar la mesa');
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert(error.message ||
                            'Error al actualizar la mesa. Por favor, intente nuevamente.');
                    } finally {
                        submitBtn.disabled = false;
                    }
                });
            }

            // Seleccionar mesa (cliente-side toggle)
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
        })();
    </script>
</body>

</html>
