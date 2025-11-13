<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <h1>Gestión de Menú</h1>
            <!-- Gestión de Items del Menú -->
            @if (session('success'))
                <div class="alert alert-success"
                    style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables">
                <div class="header" style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                    <button id="new-item-btn" class="btn-primary button-Add">
                        <span class="material-icons-sharp" style="font-size:1.3rem;">add</span>
                        Nuevo Item
                    </button>
                </div>

                <!-- Lista del menú -->
                <div class="menu-grid">
                    @foreach ($itemsMenu as $item)
                        <div class="menu-card">
                            <div class="card-content">
                                <div class="card-header">
                                    <h3>{{ $item->nombre }}</h3>
                                    <span class="price">S/. {{ number_format($item->precio, 1) }}</span>
                                </div>
                                <p class="description">{{ $item->descripcion }}</p>
                                <div class="card-footer">
                                    <span class="category">
                                        <span class="material-icons-sharp">restaurant_menu</span>
                                       {{ $item->items_categoria->nombre ?? 'Sin categoría' }}
                                    </span>
                                    <span class="status {{ $item->disponible ? 'available' : 'unavailable' }}">
                                        <span class="material-icons-sharp">
                                            {{ $item->disponible ? 'check_circle' : 'cancel' }}
                                        </span>
                                        {{ $item->disponible ? 'Disponible' : 'No disponible' }}
                                    </span>
                                </div>
                                <div class="card-actions">
                                    <button class="edit-btn" onclick="editItem({{ $item->id }})">
                                        <span class="material-icons-sharp">edit</span>
                                        Editar
                                    </button>
                                    <button class="delete-btn" onclick="deleteItem({{ $item->id }})">
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

                    <form id="itemForm" method="POST" novalidate
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
                                </select>
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <label for="disponible"
                                    style="font-size:0.9rem; font-weight:500; color:#334155;">Disponibilidad</label>
                                <select id="disponible" name="disponible" aria-label="Disponibilidad"
                                    style="padding:0.7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.95rem; background-color:#fff;">
                                    <option value="1" selected>Disponible</option>
                                    <option value="0">No disponible</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" id="cancel-mesa">Cancelar</button>
                            <button type="submit">Crear Item Menú</button>
                        </div>
                    </form>
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

        // Open modal on new item button click
        newItemBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            itemForm.reset();
            document.getElementById('method').value = 'POST';
            itemForm.action = '{{ route('items_menus.store') }}';

        });

        // Close modal functionality
        function hideModal() {
            modal.style.display = 'none';
        }

        if (closeModal) {
            closeModal.addEventListener('click', hideModal);
        }

        // Cerrar al hacer click fuera del contenido
        modal && modal.addEventListener('click', function(e) {
            if (e.target === modal) hideModal();
        });

        // Cancel button functionality
        const cancelButton = document.getElementById('cancel-mesa');
        if (cancelButton) {
            cancelButton.addEventListener('click', hideModal);
        }
    </script>

    <style>
        /* Modal improvements: modern, clean, rounded */
        #itemModal {
            display: none;
            /* Asegura que el modal esté oculto por defecto */
        }

        #itemModal .modal-content {
            background-color: var(--color-white, #ffffff);
            margin: 4% auto;
            padding: 1.25rem;
            border-radius: var(--border-radius-2, 12px);
            width: 92%;
            max-width: 640px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.04);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        #itemModal .modal-header {
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        #itemModal h2 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--color-dark, #222);
            letter-spacing: -0.2px;
        }

        /* Form layout */
        #itemModal form {
            display: grid;
            gap: 0.75rem;
        }

        /* Two-column grid for larger viewports */
        @media (min-width: 560px) {
            #itemModal .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            #itemModal .full-width {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .form-group label {
            font-size: 0.875rem;
            color: var(--color-dark, #222);
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            appearance: none;
            -webkit-appearance: none;
            padding: 0.6rem 0.75rem;
            border-radius: var(--border-radius-1, 8px);
            border: 1px solid var(--color-info-dark, #d1d7dd);
            background: var(--color-white, #fff);
            font-size: 0.95rem;
            color: var(--color-dark, #222);
            outline: none;
            transition: box-shadow .12s ease, border-color .12s ease;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 84px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--color-primary, #1e90ff);
            box-shadow: 0 4px 14px rgba(30, 144, 255, 0.08);
        }

        .field-note {
            font-size: 0.78rem;
            color: var(--color-muted, #6b7280);
            margin-top: 0.25rem;
        }

        .modal-footer {
            margin-top: 0.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-primary, #0b74de);
            color: #fff;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(11, 116, 222, 0.14);
            transition: transform .06s ease, box-shadow .06s ease, opacity .12s;
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        .btn-primary:disabled {
            opacity: .6;
            cursor: default;
            box-shadow: none;
        }

        .btn-secondary {
            background: var(--color-light, #f3f4f6);
            color: var(--color-dark, #222);
            padding: 0.55rem 0.9rem;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            cursor: pointer;
        }

        /* Menu Grid Styles */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1rem 0;
        }

        .menu-card {
            background: var(--color-white);
            border-radius: var(--border-radius-2);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover, 0 8px 24px rgba(0, 0, 0, 0.12));
        }

        .card-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-dark);
            margin: 0;
        }

        .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .description {
            color: var(--color-dark-variant);
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .category,
        .status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--color-dark-variant);
        }

        .category .material-icons-sharp,
        .status .material-icons-sharp {
            font-size: 1.1rem;
        }

        .status.available {
            color: var(--color-success, #2ecc71);
        }

        .status.unavailable {
            color: var(--color-danger, #e74c3c);
        }

        .card-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            border-top: 1px solid var(--color-light);
            padding-top: 1rem;
        }

        .card-actions button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-1);
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: var(--color-info-light, #ecf0f1);
            color: var(--color-dark);
        }

        .delete-btn {
            background: var(--color-danger-light, #ffecec);
            color: var(--color-danger, #e74c3c);
        }

        .card-actions button:hover {
            filter: brightness(0.95);
        }

        /* Dark mode adjustments */
        .dark-mode-variables .menu-card {
            background: var(--color-dark);
            border: 1px solid var(--color-dark-variant);
        }

        .dark-mode-variables .card-header h3,
        .dark-mode-variables .description {
            color: var(--color-white);
        }

        .dark-mode-variables .category,
        .dark-mode-variables .status {
            color: var(--color-info-light);
        }

        .dark-mode-variables .card-actions {
            border-top-color: var(--color-dark-variant);
        }

        .dark-mode-variables .edit-btn {
            background: var(--color-dark-variant);
            color: var(--color-white);
        }

        .dark-mode-variables .delete-btn {
            background: var(--color-danger-dark, #700303);
            color: var(--color-danger-light);
        }
    </style>
</body>

</html>
