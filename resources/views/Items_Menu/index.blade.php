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
    <style>
        .card-image {
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            background: #f6f8fa;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        /* modal: centrar y limitar altura para evitar que crezca la card de editar */
        .mesa-modal {
            display: none;
            position: fixed;
            inset: 0;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
            padding: 1rem;
        }

        .mesa-modal[style*="display:flex"] {
            display: flex;
        }

        .modal-content {
            width: 500px;
            max-width: 95%;
            padding: 2rem;
            border-radius: 12px;
            position: relative;
            box-sizing: border-box;
            max-height: 80vh;
            overflow-y: auto;
            background: #fff;
        }

        /* limitar vista previa de imagen dentro del modal */
        #imagenPreview {
            max-height: 220px;
            width: auto;
            height: auto;
            object-fit: cover;
            display: block;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Sidebar Section -->
        @include('partials.sidebar')
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Gestión Menú</h1>
            @php $role = session('empleado_cargo') ?? (session('usuario_nombre') ? 'Usuario' : null); @endphp
            <!-- Gestión de Items del Menú -->
            @if (session('success'))
                <div class="alert alert-success"
                    style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables ">
                <div class="header" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; ">
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
                        @if(in_array($role, ['Administrador','Usuario','Empleado']))
                        <button type="button" id="new-category-btn" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <span class="material-icons-sharp" style="font-size:1.1rem;">add</span>
                            Crear Categoría
                        </button>
                        @endif

                        {{-- Botón para crear nuevos items --}}
                        @if(in_array($role, ['Administrador','Usuario','Empleado']))
                        <button id="new-item-btn" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <span class="material-icons-sharp" style="font-size:1.1rem;">add</span>
                            Nuevo Item
                        </button>
                        @endif

                        {{-- Botón para Mostrar QR de Carta --}}
                        @php
                            // Determine a restaurant id to use for generating the QR.
                            // Try a passed-in $restaurante, otherwise try the first item's restaurante_id, else null.
                            $restauranteId = $restaurante->id ?? ($itemsMenu->count() ? $itemsMenu->first()->restaurante_id : null);
                        @endphp

                        <button id="open-qr-modal" type="button" data-restaurante="{{ $restauranteId ?? '' }}" class="btn-primary button-Add"
                            style="display:inline-flex; align-items:center; gap:0.5rem; text-decoration:none; padding:0.5rem 1rem; background-color:#3b82f6; color:#fff; border-radius:8px; border:none;">
                            <span class="material-icons-sharp" style="font-size:1.1rem;">qr_code</span>
                            Ver QR Carta
                        </button>
                    </div>
                </div>

                <!-- Lista del menú -->
                <div class="menu-grid">
                    @foreach ($itemsMenu as $item)
                        @php
                            // usar la URL ya resuelta por el controlador; si no existe, generar con Storage::url()
                            $imgSrc = $item->imagen_url ?? null;
                            if (! $imgSrc && ! empty($item->imagen)) {
                                $imgSrc = \Illuminate\Support\Facades\Storage::url($item->imagen);
                            }
                        @endphp

                        <div class="menu-card">
                            @if ($imgSrc)
                                <div class="card-image">
                                    <img src="{{ $imgSrc }}" alt="{{ $item->nombre }}">
                                </div>
                            @endif

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
                                    @if(in_array($role, ['Administrador','Usuario']))
                                    <button class="delete-btn" onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->nombre) }}')">
                                        <span class="material-icons-sharp">delete</span>
                                        Eliminar
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal para crear nuevo Menú-->
            <div id="itemModal" class="mesa-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1"
                aria-labelledby="itemModalTitle">

                <div class="modal-content"
                    style="padding:2rem; border-radius:12px; width:500px; max-width:95%; position:relative; max-height:80vh; overflow-y:auto; box-sizing:border-box;">
                    <div class="modal-header" style="margin-bottom:1.5rem;">
                        <h2 id="itemModalTitle" class="label-dark">Crear Item del Menú</h2>
                        <button id="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                    </div>

                    <form id="itemForm" method="POST" novalidate action="{{ route('items_menus.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="method">

                        <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group">
                                    <label for="nombre" class="label-dark">Nombre del Item</label>
                                    <input type="text" id="nombre" name="nombre" required
                                        placeholder="Ej. Lomo saltado" autocomplete="off"
                                        style="padding:0.7rem;  border-radius:8px; font-size:0.95rem;">
                                </div>

                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group">
                                    <label for="precio" class="label-dark">Precio</label>
                                    <input type="number" id="precio" name="precio" step="0.01" required
                                        placeholder="0.00" inputmode="decimal"
                                        style="padding:0.7rem;  border-radius:8px; font-size:0.95rem;">
                                </div>
                            </div>

                            <div class="form-group"
                                style="grid-column:1/-1; display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group">
                                    <label for="descripcion" class="label-dark">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" required rows="3" placeholder="Breve descripción del plato"
                                        style="padding:0.7rem; border-radius:8px; font-size:0.95rem; resize:vertical;"></textarea>
                                </div>
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group" <label for="categoria_id" class="label-dark">
                                    Categoría</label>
                                    <select id="categoria_id" name="categoria_id" required
                                        style="padding:0.7rem; border-radius:8px; font-size:0.95rem;">
                                        <option value="" disabled selected>Seleccione una categoría</option>
                                        @if (isset($categorias) && $categorias->count())
                                            @foreach ($categorias as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group">
                                    <label for="estado" class="label-dark">Disponibilidad</label>
                                    <select id="estado" name="estado" aria-label="Disponibilidad"
                                        style="padding:0.7rem; border-radius:8px; font-size:0.95rem;">
                                        <option value="disponible" selected>Disponible</option>
                                        <option value="no_disponible">No disponible</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Imagen (opcional) -->
                            <div class="form-group"
                                style="grid-column:1/-1; display:flex; flex-direction:column; gap:0.5rem;">
                                <div class="input-group">
                                    <label for="imagen" class="label-dark">Imagen (opcional)</label>
                                    <input type="file" id="imagen" name="imagen" accept="image/*"
                                        style="padding:0.25rem;">
                                    <div style="margin-top:0.5rem;">
                                        <img id="imagenPreview" src="" alt="Preview imagen"
                                            style="max-width:100%; max-height:160px; display:none; border-radius:8px; object-fit:cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-actions">
                            <button type="submit">Crear Item Menú</button>
                            <button type="button" id="cancel-modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de confirmación de eliminación de item -->
            <div id="delete-item-modal" class="mesa-modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-icon material-icons-sharp" style="color:#f59e0b;">warning</span>
                        <h2 style="margin:0;" class="label-dark">Confirmar eliminación</h2>
                    </div>
                    <div style="padding:0.5rem 0 1rem 0;">
                        <p id="delete-item-message">¿Desea eliminar este item?</p>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:0.6rem; margin-top:1rem;">
                        <button type="button" id="delete-item-cancel" class="button-Add edit-btn"
                            style="background:#e2e8f0; color:#374151; border:none; padding:0.6rem 1rem; border-radius:6px;">Cancelar</button>
                        @if(in_array($role, ['Administrador','Usuario']))
                        <button type="button" id="delete-item-confirm" class="button-Add delete-btn"
                            style="background:#e53935; color:#fff; border:none; padding:0.6rem 1rem; border-radius:6px;">Eliminar</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Modal que muestra el QR --}}
            <div id="qr-modal" class="mesa-modal" style="display:none;">
                <div class="modal-content" style="max-width:400px; text-align:center;">
                    <div class="modal-header">
                        <span class="modal-icon material-icons-sharp">qr_code</span>
                        <h2 style="margin:0; color: var(--primary-color);">QR de la Carta</h2>
                    </div>
                    <div style="padding:1rem 0;">
                        <img id="qr-image" src="" alt="QR Carta" 
                            style="width:200px; height:200px; justify-self:center;">
                        <p id="qr-link-wrapper" style="margin-top:1rem; font-size:0.9rem; color:#475569;">
                            <a id="qr-link" href="#" target="_blank" rel="noopener noreferrer"></a>
                            <button id="copy-qr-link" class="button-Add" style="justify-self:center; border:none; padding:4px 8px; border-radius:6px;">Copiar</button>
                        </p>
                        <p style="margin-top:0.5rem; font-size:0.85rem; color:#64748b;">Escanea o abre este enlace para acceder a la carta del menú.</p>
                    </div>
                    <div style="display:flex; justify-content:center; margin-top:1rem;">
                        <button type="button" id="close-qr-modal" class="button-Add edit-btn"
                            style="background:#3b82f6; color:#fff; border:none; padding:0.6rem 1rem; border-radius:6px;">Cerrar</button>
                    </div>
                </div>
            </div>

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        @include('partials.right-section')

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
        const qrModal = document.getElementById('qrModal');

        // QR modal functionality
        const openQrModalBtn = document.getElementById('open-qr-modal');
        const closeQrModalBtn = document.getElementById('close-qr-modal');
        const qrModalEl = document.getElementById('qr-modal');
        const qrImageEl = document.getElementById('qr-image');
        const qrLinkEl = document.getElementById('qr-link');
        const copyQrBtn = document.getElementById('copy-qr-link');

        if (closeQrModalBtn) {
            closeQrModalBtn.addEventListener('click', function() {
                if (qrModalEl) qrModalEl.style.display = 'none';
            });
        }

        if (openQrModalBtn) {
            openQrModalBtn.addEventListener('click', function() {
                // Get the restaurante id from data attribute or fallback to first item
                const restauranteId = this.dataset.restaurante || ({{ $itemsMenu->count() ? $itemsMenu->first()->restaurante_id : 'null' }});
                if (!restauranteId) {
                    alert('No hay restaurante configurado para generar la carta.');
                    return;
                }

                // Open modal and show loading placeholder
                if (qrModalEl) qrModalEl.style.display = 'flex';
                qrImageEl.src = ''; // reset

                fetch(`/qr/${restauranteId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        const text = await response.text().catch(() => null);
                        throw new Error(text || 'Error al generar QR');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.qr_url) {
                        throw new Error('No se recibió URL de QR del servidor');
                    }
                    qrImageEl.src = data.qr_url;
                    qrLinkEl.href = data.menu_url;
                    qrLinkEl.textContent = data.menu_url;
                })
                .catch(err => {
                    console.error('Error generando QR:', err);
                    alert('No se pudo generar el QR. Intente nuevamente más tarde.');
                    if (qrModalEl) qrModalEl.style.display = 'none';
                });
            });
        }

        // Copy menu link
        if (copyQrBtn) {
            copyQrBtn.addEventListener('click', function() {
                const link = qrLinkEl && qrLinkEl.href;
                if (!link) return;
                navigator.clipboard?.writeText(link).then(() => {
                    alert('Enlace copiado al portapapeles');
                }).catch(() => {
                    prompt('Copiar enlace manualmente:', link);
                });
            });
        }

        // Open modal on new item button click
        newItemBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            itemForm.reset();
            document.getElementById('method').value = 'POST';
            itemForm.action = '{{ route('items_menus.store') }}';
            itemModalTitle.textContent = 'Crear Item del Menú';
            submitBtn.textContent = 'Crear Item Menú';
            if (imagenPreview) { imagenPreview.src = ''; imagenPreview.style.display = 'none'; }
        });

        // Close modal functionality
        function hideModal() {
            modal.style.display = 'none';
            itemForm.reset();
            if (imagenPreview) { imagenPreview.src = ''; imagenPreview.style.display = 'none'; }
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

                    // manejar preview de imagen usando imagen_url
                    if (imagenPreview) {
                        const src = item.imagen_url || (item.imagen ? (String(item.imagen).startsWith('http') ? item.imagen : `/storage/${item.imagen}`) : '');
                        if (src) {
                            imagenPreview.src = src;
                            imagenPreview.style.display = 'block';
                        } else {
                            imagenPreview.src = '';
                            imagenPreview.style.display = 'none';
                        }
                    }

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
                // usar imagen_url si viene desde el servidor, fallback a ruta relativa
                const imgSrc = item.imagen_url || (item.imagen ? (String(item.imagen).startsWith('http') ? item.imagen : `/storage/${item.imagen}`) : '');
                const imgHtml = imgSrc ? `<div class="card-image"><img src="${imgSrc}" alt="${String(item.nombre).replace(/"/g, '&quot;')}"></div>` : '';
                return `
                <div class="menu-card">
                    <div class="card-content">
                        ${imgHtml}
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

        // obtener elementos de imagen
        const imagenInput = document.getElementById('imagen');
        const imagenPreview = document.getElementById('imagenPreview');

        // preview local al seleccionar archivo
        if (imagenInput) {
            imagenInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    imagenPreview.src = '';
                    imagenPreview.style.display = 'none';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagenPreview.src = e.target.result;
                    imagenPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        }
    </script>

</body>

</html>
