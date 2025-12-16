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
        /* Modal: no scroll propio, el scroll estará sólo en el listado de items */
        .mesa-modal {
            padding: 2rem 0;
        }

        .modal-content {
            margin: auto;
            max-height: 90vh;
        }
        /* Asegurar que la lista de items tenga scroll independiente */
        #items-list-wrapper { overflow:auto; max-height:350px; }
    </style>
</head>

<body>
    <script src="{{ asset('index.js') }}"></script>
    <div class="container">
        <!-- Sidebar Section -->
        @include('partials.sidebar')
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
                        <button id="refresh-mesas" class="btn-primary button-Add">
                            <span>Refrescar</span>
                        </button>
                    </div>
                </div>

                <div class="mesas-grid">
                    @if (isset($mesas) && $mesas->count())
                        @foreach ($mesas as $mesa)
                            <div class="mesa-card card-mesa estado-{{ strtolower($mesa->estado) }}"
                                data-mesa-id="{{ $mesa->id }}">
                                <div class="icon">
                                    <span class="material-icons-sharp">table_restaurant</span>
                                </div>
                                <h3>{{ $mesa->nombre }}</h3>
                                <div class="estado-container">
                                    <div class="estado-indicador">
                                        {{ $mesa->estado }}
                                    </div>
                                </div>
                                @if (!empty($mesa->detalle_reserva) && $mesa->estado === 'Reservada')
                                    <p class="reservation-detail filtro_reserva"
                                        style="font-size:0.8rem;  margin-top:0.4rem;">Reserva:
                                        {{ $mesa->detalle_reserva }}</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p>No hay mesas registradas.</p>
                    @endif
                </div>
            </div>

            <!-- Modal para crear comanda -->
            <div id="create-comanda-modal" class="mesa-modal" role="dialog" aria-modal="true" aria-hidden="true"
                tabindex="-1" style="display:none;">
                <div class="modal-content"
                    style="
                        padding:2rem;
                        border-radius:12px;
                        width:800px;
                        height:600px;
                        max-width:none;
                        max-height:none;
                        overflow:auto;
                        box-sizing:border-box;">
                    <button id="close-create-comanda" class="btn" aria-label="Cerrar">
                        <span class="material-icons-sharp">close</span>
                    </button>
                    <div class="modal-header">
                        <span class="modal-icon material-icons-sharp">receipt_long</span>
                        <h2 id="create-comanda-title" class="label-dark">Crear Comanda</h2>
                    </div>

                    <form id="create-comanda-form" method="POST" action="{{ route('comandas.store') }}">
                        @csrf
                        <input type="hidden" name="mesa_id" id="mesa_id">
                        <input type="hidden" name="estado" value="abierta">

                        <div class="form-group" style="display:flex; gap:1rem; flex-wrap:wrap;">
                            <div style="flex:1;">

                                <div class="input-group" style="margin-bottom:0.75rem;">
                                    <div style="display:flex; gap:0.5rem; align-items:flex-end; flex-wrap:wrap;">
                                        <div style="flex:1; min-width:180px;">
                                            <h3>Seleccionar Producto</h3>
                                            <select id="select-item" class="form-control"
                                                style="width:100%; padding:.5rem;">
                                                <option value="">-- Seleccione Item --</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-price="{{ $item->precio }}">
                                                        {{ $item->nombre }} -
                                                        ${{ number_format($item->precio, 0, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div style="width:220px; display:flex; gap:0.5rem; align-items:flex-end;">
                                            <div style="display:flex; flex-direction:column; width:100%;">
                                                <label for="item-cantidad" class="label-dark"
                                                    style="font-size:0.85rem;">Cantidad:</label>
                                                <div style="display:flex; gap:0.5rem; align-items:center;">
                                                    <input id="item-cantidad" type="number" min="1"
                                                        value="1" class="form-control" style="width:80px;" />
                                                    <button type="button" id="add-item-btn" class="button-Add"
                                                        style="height:38px;">Agregar</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div id="items-list-wrapper" style="max-height:350px; overflow:auto;">
                                <table class="table order-table" style="width:100%;">
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

                                    </tbody>
                                </table>
                            </div>

                            <div class="form-actions"
                                style="display:flex; justify-content:space-between; align-items:center; margin-top:0.75rem;">
                                <strong>Total:</strong>
                                <strong id="order-total">$0</strong>
                            </div>

                        </div>

                        <div style="flex: 0 0 320px; min-width:240px;"><br>
                            <h3>Información Comanda</h3>
                            <div class="input-group" style="display:flex; flex-direction:column; gap:0.5rem;"><br>
                                <label for="observaciones_global" class="label-dark">Observaciones (comanda)</label>
                                <textarea id="observaciones_global" name="observaciones_global" rows="3"
                                    placeholder="Observaciones generales" style="border-radius:8px solid; font-size:0.95rem"></textarea>

                                <input type="hidden" name="order_items" id="order_items_input" />

                                <div class="form-actions"
                                    style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                                    <button type="button" id="cancel-create-comanda"
                                        class="btn">Cancelar</button>
                                    <button type="submit" style=" justify-content: center;" class="submit-btn">Crear Comanda</button>
                                </div>
                            </div>
                        </div>
                </div>
                </form>

            </div>
    </main>
    <!-- End of Main Content -->

    @include('partials.right-section')

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
                        <td style="text-align:center;"><button type="button" class="remove-item btn-remove" data-index="${index}" aria-label="Quitar"><span class="material-icons-sharp">delete</span></button></td>
                    </tr>`).join('');

                // attach events for remove buttons and obs updates
                document.querySelectorAll('.remove-item').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const idx = parseInt(e.currentTarget.dataset.index);
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
                    modal.setAttribute('aria-hidden', 'false');
                    // focus primer campo útil
                    const firstInput = modal.querySelector('#select-item');
                    if (firstInput) firstInput.focus();
                });
            });

            function hideModal() {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
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
                    orderItems.push({
                        item_id: itemId,
                        nombre,
                        precio,
                        cantidad,
                        observaciones: ''
                    });
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
                orderItemsInput.value = JSON.stringify(orderItems.map(i => ({
                    item_id: i.item_id,
                    cantidad: i.cantidad,
                    observaciones: i.observaciones,
                    valor_item_ATM: i.precio
                })));
            });

            // Refresh mesas (reload page or call an endpoint).
            const refreshBtn = document.getElementById('refresh-mesas');
            if (refreshBtn) refreshBtn.addEventListener('click', () => location.reload());

            // close modal by clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) hideModal();
            });
        })();
    </script>
</body>

</html>
