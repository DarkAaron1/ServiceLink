<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink - Menú</title>
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
                <a href="">
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
                <a href="{{ route('items_menu.index') }} " class="active">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Menú</h3>
                </a>
                <a href="{{ route('mesas.index') }}">
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
            <h1>Gestión Menú</h1>
            <!-- Gestión de Items del Menú -->
            @if (session('success'))
                <div class="alert alert-success"
                    style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables ">
                <div class="header" style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <label for="filter_categoria" class="filtro_cat">Filtrar por categoría:</label>
                        <select id="filter_categoria"
                            style="padding:0.5rem; border-radius:8px; border:1px solid #e2e8f0; background:#fff;">
                            <option value="all">Todas</option>
                            @if (isset($categorias) && $categorias->count())
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; justify-content:flex-end;">
                        {{-- Botón para crear nuevas categorias --}}
                        <button type="button" id="new-category-btn" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <span class="material-icons-sharp" style="font-size:1.1rem;">add</span>
                            Crear Categoría
                        </button>

                        {{-- Botón para crear nuevos items --}}
                        <button id="new-item-btn" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <span class="material-icons-sharp" style="font-size:1.1rem;">add</span>
                            Nuevo Item
                        </button>
                    </div>
                </div>

                <!-- Lista del menú -->
                <div class="menu-grid">
                    @foreach ($itemsMenu as $item)
                        <div class="menu-card">
                            <div class="card-content">
                                <div class="card-header">
                                    <h3>{{ $item->nombre }}</h3>
                                    <span class="price"> $ {{ number_format($item->precio, 1) }}</span>
                                </div>
                                <p class="description">{{ $item->descripcion }}</p>
                                <div class="card-footer">
                                    <span class="category">
                                        <span class="material-icons-sharp">restaurant_menu</span>
                                        {{ $item->categoria ? $item->categoria->nombre : 'Sin categoría' }}
                                    </span>
                                    <span
                                        class="status {{ $item->estado === 'disponible' ? 'available' : 'unavailable' }}">
                                        <span class="material-icons-sharp">
                                            {{ $item->estado === 'disponible' ? 'check_circle' : 'cancel' }}
                                        </span>
                                        {{ $item->estado === 'disponible' ? 'Disponible' : 'No disponible' }}
                                    </span>

                                </div>
                                <div class="card-actions">
                                    <button class="edit-btn" onclick="editItem({{ $item->id }})">
                                        <span class="material-icons-sharp">edit</span>
                                        Editar
                                    </button>
                                    <button class="delete-btn"
                                        onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->nombre) }}')">
                                        <span class="material-icons-sharp">delete</span>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal para crear nuevo-->
            <div id="itemModal" class="modal"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); align-items:center; justify-content:center; z-index:1000;"
                role="dialog" aria-modal="true" aria-labelledby="itemModalTitle">

                <div class="modal-content"
                    style="background:#fff; padding:2rem; border-radius:12px; width:500px; max-width:95%; position:relative; box-shadow:0 15px 40px rgba(0,0,0,0.15);">
                    <div class="modal-header" style="margin-bottom:1.5rem;">
                        <h2 id="itemModalTitle" style="font-size:1.4rem; color:#2c3e50;">Crear Item del Menú</h2>
                        <button id="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                    </div>

                    <form id="itemForm" method="POST" novalidate action="{{ route('items_menus.store') }}"
                        style="display:flex; flex-direction:column; gap:1.2rem;">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="method">

                        <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="nombre" style="font-size:0.9rem; font-weight:500; color:#334155;">Nombre
                                    del Item
                                </label>
                                <input type="text" id="nombre" name="nombre" required
                                    placeholder="Ej. Lomo saltado" autocomplete="off"
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem;">
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="precio"
                                    style="font-size:0.9rem; font-weight:500; color:#334155;">Precio</label>
                                <input type="number" id="precio" name="precio" step="0.01" required
                                    placeholder="0.00" inputmode="decimal"
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem;">
                                <div class="field-note" style="font-size:0.8rem; color:#64748b;">Ingrese el precio en
                                    la
                                    moneda local.</div>
                            </div>

                            <div class="form-group"
                                style="grid-column:1/-1; display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="descripcion"
                                    style="font-size:0.9rem; font-weight:500; color:#334155;">Descripción</label>
                                <textarea id="descripcion" name="descripcion" required rows="3" placeholder="Breve descripción del plato"
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem; resize:vertical;"></textarea>
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="categoria_id"
                                    style="font-size:0.9rem; font-weight:500; color:#334155;">Categoría</label>
                                <select id="categoria_id" name="categoria_id" required
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem; background-color:#fff;">
                                    <option value="" disabled selected>Seleccione una categoría</option>
                                    @if (isset($categorias) && $categorias->count())
                                        @foreach ($categorias as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="estado"
                                    style="font-size:0.9rem; font-weight:500; color:#334155;">Disponibilidad</label>
                                <select id="estado" name="estado" aria-label="Disponibilidad"
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem; background-color:#fff;">
                                    <option value="disponible" selected>Disponible</option>
                                    <option value="no_disponible">No disponible</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" id="cancel-modal">Cancelar</button>
                            <button type="submit">Crear Item Menú</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de confirmación de eliminación de item -->
            <div id="delete-item-modal" class="mesa-modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-icon material-icons-sharp" style="color:#f59e0b;">warning</span>
                        <h2 style="margin:0;">Confirmar eliminación</h2>
                    </div>
                    <div style="padding:0.5rem 0 1rem 0;">
                        <p id="delete-item-message">¿Desea eliminar este item?</p>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:0.6rem; margin-top:1rem;">
                        <button type="button" id="delete-item-cancel" class="button-Add edit-btn"
                            style="background:#e2e8f0; color:#374151; border:none; padding:0.6rem 1rem; border-radius:6px;">Cancelar</button>
                        <button type="button" id="delete-item-confirm" class="button-Add delete-btn"
                            style="background:#e53935; color:#fff; border:none; padding:0.6rem 1rem; border-radius:6px;">Eliminar</button>
                    </div>
                </div>
            </div>




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

    <script src="{{ asset('index.js') }}"></script>
    <script>
        // Modal functionality
        const modal = document.getElementById('itemModal');
        const newItemBtn = document.getElementById('new-item-btn');
        const closeModal = document.getElementById('close-modal');
        const itemForm = document.getElementById('itemForm');
        const itemModalTitle = document.getElementById('itemModalTitle');
        const submitBtn = itemForm.querySelector('button[type="submit"]');

        // Open modal on new item button click
        newItemBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            itemForm.reset();
            document.getElementById('method').value = 'POST';
            itemForm.action = '{{ route('items_menus.store') }}';
            itemModalTitle.textContent = 'Crear Item del Menú';
            submitBtn.textContent = 'Crear Item Menú';
        });

        // Close modal functionality
        function hideModal() {
            modal.style.display = 'none';
            itemForm.reset();
        }

        if (closeModal) {
            closeModal.addEventListener('click', hideModal);
        }

        // Cerrar al hacer click fuera del contenido
        modal && modal.addEventListener('click', function(e) {
            if (e.target === modal) hideModal();
        });

        // Cancel button functionality
        const cancelButton = document.getElementById('cancel-modal');
        if (cancelButton) {
            cancelButton.addEventListener('click', hideModal);
        }

        // Edit item function
        function editItem(itemId) {
            fetch(`/items_menus/${itemId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(item => {
                    document.getElementById('nombre').value = item.nombre;
                    document.getElementById('precio').value = item.precio;
                    document.getElementById('descripcion').value = item.descripcion;
                    document.getElementById('categoria_id').value = item.categoria_id;
                    document.getElementById('estado').value = item.estado;

                    itemModalTitle.textContent = 'Editar Item del Menú';
                    submitBtn.textContent = 'Actualizar Item Menú';
                    itemForm.action = `/items_menus/${itemId}`;
                    document.getElementById('method').value = 'PATCH';

                    modal.style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar el item');
                });
        }
        // Delete item function (opens confirmation modal)
        function deleteItem(itemId, itemName) {
            const deleteModal = document.getElementById('delete-item-modal');
            const deleteMessage = document.getElementById('delete-item-message');
            const deleteConfirm = document.getElementById('delete-item-confirm');
            const deleteCancel = document.getElementById('delete-item-cancel');

            if (!deleteModal || !deleteConfirm || !deleteCancel || !deleteMessage) {
                // Fallback to previous behaviour
                if (!confirm('¿Está seguro de que desea eliminar este item?')) return;
            }

            deleteMessage.textContent = itemName ? `¿Desea eliminar el item "${itemName}"?` : '¿Desea eliminar este item?';
            deleteModal.style.display = 'flex';

            // Cancel handler
            deleteCancel.onclick = function() {
                deleteModal.style.display = 'none';
            };

            // Remove any previous handler and set new one
            deleteConfirm.onclick = null;
            deleteConfirm.onclick = function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/items_menus/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type') || '';
                        let payload = null;
                        if (contentType.includes('application/json')) {
                            payload = await response.json().catch(() => null);
                        } else {
                            const text = await response.text().catch(() => null);
                            try {
                                payload = text ? JSON.parse(text) : null;
                            } catch (e) {
                                payload = null;
                            }
                        }

                        if (response.ok) {
                            // Treat OK as success
                            deleteModal.style.display = 'none';
                            location.reload();
                        } else {
                            const msg = (payload && payload.message) ? payload.message :
                                'Error al eliminar el item';
                            deleteModal.style.display = 'none';
                            alert(msg);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        deleteModal.style.display = 'none';
                        alert('Error al eliminar el item');
                    });
            };
        }


        // Form submission
        itemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const method = document.getElementById('method').value;

            let fetchOptions = {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document
                        .querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            };

            if (method === 'POST') {
                fetchOptions.method = 'POST';
                fetchOptions.body = formData;
            } else if (method === 'PATCH') {
                fetchOptions.method = 'POST';
                formData.append('_method', 'PATCH');
                fetchOptions.body = formData;
            }

            fetch(this.action, fetchOptions)
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
                    alert('Error en la operación');
                });
        });

        // Render items helper for AJAX filtering
        function renderItems(items) {
            const grid = document.querySelector('.menu-grid');
            if (!grid) return;
            if (!items || !items.length) {
                grid.innerHTML = '<p>No hay items para esta categoría.</p>';
                return;
            }
            grid.innerHTML = items.map(item => {
                const categoriaNombre = item.categoria ? item.categoria.nombre : 'Sin categoría';
                const estadoClass = item.estado === 'disponible' ? 'available' : 'unavailable';
                const estadoIcon = item.estado === 'disponible' ? 'check_circle' : 'cancel';
                const estadoText = item.estado === 'disponible' ? 'Disponible' : 'No disponible';
                return `
                <div class="menu-card">
                    <div class="card-content">
                        <div class="card-header">
                            <h3>${item.nombre}</h3>
                            <span class="price">$ ${Number(item.precio).toFixed(1)}</span>
                        </div>
                        <p class="description">${item.descripcion || ''}</p>
                        <div class="card-footer">
                            <span class="category">
                                <span class="material-icons-sharp">restaurant_menu</span>
                                ${categoriaNombre}
                            </span>
                            <span class="status ${estadoClass}">
                                <span class="material-icons-sharp">${estadoIcon}</span>
                                ${estadoText}
                            </span>
                        </div>
                        <div class="card-actions">
                            <button class="edit-btn" onclick="editItem(${item.id})">
                                <span class="material-icons-sharp">edit</span>Editar
                            </button>
                            <button class="delete-btn" onclick="deleteItem(${item.id}, '${String(item.nombre).replace(/'/g, "\\'")}')">
                                <span class="material-icons-sharp">delete</span>Eliminar
                            </button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        // Filter by category select
        const filterSelect = document.getElementById('filter_categoria');
        if (filterSelect) {
            filterSelect.addEventListener('change', function() {
                const val = this.value;
                const url = val === 'all' ? '/items_menus/categoria/all' : `/items_menus/categoria/${val}`;
                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        renderItems(data);
                    })
                    .catch(err => {
                        console.error('Error al filtrar:', err);
                        alert('Error al filtrar por categoría');
                    });
            });
        }


        // redireccionar a  category button
        document.getElementById('new-category-btn').addEventListener('click', function() {
            window.location.href = "{{ route('categorias.index') }}";
        });
    </script>

</body>

</html>
