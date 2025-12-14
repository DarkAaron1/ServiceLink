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
        /* Modal scrollable */
        .mesa-modal {
            overflow-y: auto;
            padding: 2rem 0;
        }

        .modal-content {
            margin: auto;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <script src="{{ asset('index.js') }}"></script>
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
                <a href="{{ url('/index') }}">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="{{ route('empleados.index') }}">
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
                <a href="{{ route('items_menu.index') }}">
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
            <h1>Gestión de Comandas</h1>

            <div class="management-tables" style="margin-bottom:2rem;">
                <div class="header" style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div>
                        <h3>Mesas disponibles</h3>
                        <p>Haz click en una mesa para abrir la creación de comanda.</p>
                    </div>
                    <div style="display:flex; gap:0.75rem; align-items:center;">
                        <button id="refresh-mesas" class="btn">Refrescar</button>
                    </div>
                </div>

                <div class="mesas-grid">
                    @foreach($mesas as $mesa)
                    <div class="card-mesa" data-mesa-id="{{ $mesa->id }}" role="button"
                        aria-label="Mesa {{ $mesa->nombre}}">
                        <div class="mesa-header">
                            <h4>Mesa {{ $mesa->nombre }}</h4>
                        </div>
                        <div class="mesa-body">
                            <p>Estado: {{ $mesa->estado ?? 'disponible' }}</p>
                            @if ($mesa->estado == 'Reservada')
                                <p>Detalle: {{ $mesa->detalle_reserva }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal para crear comanda -->
            <div id="create-comanda-modal" class="mesa-modal" style="display:none;" role="dialog" aria-modal="true">
                <div class="modal-content">
                    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <h2 id="create-comanda-title">Crear Comanda</h2>
                        <button id="close-create-comanda" class="btn">Cerrar</button>
                    </div>

                    <form id="create-comanda-form" method="POST" action="{{ route('comandas.store') }}">
                        @csrf
                        <input type="hidden" name="mesa_id" id="mesa_id">
                        <input type="hidden" name="estado" value="abierta">

                        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                            <div style="flex:1; min-width:280px;">
                                <h3>Seleccionar Producto</h3>
                                <div style="display:flex; gap:0.5rem; align-items:center; margin-bottom: 0.75rem;">
                                    <select id="select-item" class="form-control" style="flex: 1; padding: .5rem;">
                                        <option value="">-- Seleccione Item --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->precio }}">{{ $item->nombre }} - ${{ number_format($item->precio, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    <input id="item-cantidad" type="number" min="1" value="1" class="form-control" style="width:80px;" />
                                    <button type="button" id="add-item-btn" class="btn">Agregar</button>
                                </div>

                                <div id="items-list-wrapper" style="max-height:350px; overflow:auto;">
                                    <table class="table" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Cant.</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                                <th>Obs.</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="order-items-body">
                                            <!-- filas agregadas por JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 0.75rem;">
                                    <strong>Total:</strong>
                                    <strong id="order-total">$0</strong>
                                </div>

                            </div>

                            <div style="flex: 0 0 320px; min-width:280px;">
                                <h3>Información Comanda</h3>
                                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                                    <label for="rut_empleado">Rut Empleado</label>
                                    <input id="rut_empleado" name="rut_empleado" type="text" class="form-control" placeholder="Ingrese rut del empleado" required />

                                    <label for="observaciones_global">Observaciones (comanda)</label>
                                    <textarea id="observaciones_global" name="observaciones_global" rows="3" class="form-control"
                                        placeholder="Observaciones generales"></textarea>

                                    <input type="hidden" name="order_items" id="order_items_input" />

                                    <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                                        <button type="button" id="cancel-create-comanda" class="btn">Cancelar</button>
                                        <button type="submit" class="btn primary">Crear Comanda</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </main>
        <!-- End of Main Content -->

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
                            <p>Bienvenido, <b>{{ $usuario->nombre ?? 'Usuario' }}</b></p>
                            <small class="text-muted">{{ $rolName ?? 'Admin' }}</small>
                        </div>
                    </div>

            </div>
            <!-- End of Nav -->

            <div class="user-profile">
                <div class="logo">
                    <img src="{{  asset('favicon.ico') }}">
                    <h2>{{ $usuario->nombre ?? 'Usuario' }}</h2>
                    <p>{{ $rolName?? 'Rol' }}</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        (function() {
            const mesaCards = document.querySelectorAll('.card-mesa');
            const modal = document.getElementById('create-comanda-modal');
            const closeModal = document.getElementById('close-create-comanda');
            const cancelModal = document.getElementById('cancel-create-comanda');
            const mesaInput = document.getElementById('mesa_id');
            const title = document.getElementById('create-comanda-title');
            const selectItem = document.getElementById('select-item');
            const itemCantidad = document.getElementById('item-cantidad');
            const addItemBtn = document.getElementById('add-item-btn');
            const orderBody = document.getElementById('order-items-body');
            const totalEl = document.getElementById('order-total');
            const orderItemsInput = document.getElementById('order_items_input');
            const form = document.getElementById('create-comanda-form');

            let orderItems = [];

            function formatCurrency(value) {
                return '$' + Number(value).toLocaleString('es-CL');
            }

            function updateTotal() {
                let total = orderItems.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
                totalEl.textContent = formatCurrency(total);
            }

            function renderOrder() {
                orderBody.innerHTML = orderItems.map((it, index) => `
                    <tr data-index="${index}">
                        <td>${it.nombre}</td>
                        <td>${it.cantidad}</td>
                        <td>${formatCurrency(it.precio)}</td>
                        <td>${formatCurrency(it.precio * it.cantidad)}</td>
                        <td><input type="text" data-index="${index}" class="form-control obs-input" value="${it.observaciones ?? ''}" placeholder="Obs. (ej: sin sal)" /></td>
                        <td><button type="button" class="btn remove-item" data-index="${index}">Quitar</button></td>
                    </tr>`).join('');

                // attach events for remove buttons and obs updates
                document.querySelectorAll('.remove-item').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const idx = parseInt(e.target.dataset.index);
                        orderItems.splice(idx, 1);
                        renderOrder();
                        updateTotal();
                    });
                });

                document.querySelectorAll('.obs-input').forEach(inp => {
                    inp.addEventListener('input', (e) => {
                        const idx = parseInt(e.target.dataset.index);
                        orderItems[idx].observaciones = e.target.value;
                    });
                });

                updateTotal();
            }

            mesaCards.forEach(card => {
                card.addEventListener('click', () => {
                    const id = card.dataset.mesaId;
                    const mesaNum = card.querySelector('.mesa-header h4')?.textContent || `Mesa ${id}`;
                    mesaInput.value = id;
                    title.textContent = `Crear Comanda - ${mesaNum}`;
                    orderItems = [];
                    renderOrder();
                    updateTotal();
                    modal.style.display = 'flex';
                });
            });

            function hideModal() {
                modal.style.display = 'none';
            }

            if (closeModal) closeModal.addEventListener('click', hideModal);
            if (cancelModal) cancelModal.addEventListener('click', hideModal);

            addItemBtn.addEventListener('click', () => {
                const itemId = selectItem.value;
                const cantidad = parseInt(itemCantidad.value) || 1;
                if (!itemId) return alert('Seleccione un item');

                const option = selectItem.querySelector(`option[value="${itemId}"]`);
                const precio = Number(option.dataset.price || 0);
                const nombre = option.textContent;

                // si ya existe, sumar cantidad
                const existingIndex = orderItems.findIndex(i => i.item_id == itemId);
                if (existingIndex >= 0) {
                    orderItems[existingIndex].cantidad += cantidad;
                } else {
                    orderItems.push({ item_id: itemId, nombre, precio, cantidad, observaciones: '' });
                }
                selectItem.value = '';
                itemCantidad.value = 1;
                renderOrder();
            });

            // prepare order items input before submit
            form.addEventListener('submit', (e) => {
                if (!orderItems.length) {
                    e.preventDefault();
                    return alert('Agregue al menos un item a la comanda');
                }
                orderItemsInput.value = JSON.stringify(orderItems.map(i => ({ item_id: i.item_id, cantidad: i.cantidad, observaciones: i.observaciones, valor_item_ATM: i.precio })));
            });

            // Refresh mesas (reload page or call an endpoint).
            const refreshBtn = document.getElementById('refresh-mesas');
            if (refreshBtn) refreshBtn.addEventListener('click', () => location.reload());

            // close modal by clicking outside
            modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });
        })();
    </script>
</body>

</html>
