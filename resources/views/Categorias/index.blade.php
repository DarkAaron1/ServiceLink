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
        @include('partials.sidebar')
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Gestión Categoría</h1>
            @php $role = session('empleado_cargo') ?? (session('usuario_nombre') ? 'Usuario' : null); @endphp
            <!-- Gestión de Items del Menú -->
            @if (session('success'))
                <div class="alert alert-success"
                    style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables ">
                <div class="header" style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                    <button class="button-volver" onclick="window.history.back()">
                        <span class="material-icons-sharp">arrow_back</span>
                        Volver
                    </button>

                    {{-- Botón para crear nuevas categorias --}}
                    @if(in_array($role, ['Administrador','Usuario','Empleado']))
                    <button id="new-category-btn" class="btn-primary button-Add" onclick="openNewCategory()">
                        <span class="material-icons-sharp" style="font-size:1.3rem;">add</span>
                        Crear Categoría
                    </button>
                    @endif

                </div>

                <!-- Lista del categoria -->
                <div class="menu-grid">
                    @if (isset($categorias) && $categorias->count())
                        @foreach ($categorias as $cat)
                            <div class="menu-card" data-id="{{ $cat->id }}">
                                <div class="card-content">
                                    <div class="card-header">
                                        <h3>{{ $cat->nombre }}</h3>
                                        <span class="price">&nbsp;</span>
                                    </div>
                                    <p class="description">{{ $cat->descripcion ?? 'Sin descripción' }}</p>

                                    <div class="card-actions">
                                        <button class="edit-btn"
                                            onclick="openEditCategory({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->descripcion ?? '') }}')">
                                            <span class="material-icons-sharp">edit</span>
                                            Editar
                                        </button>
                                          @if(in_array($role, ['Administrador','Usuario']))
                                            <form method="POST" action="{{ url('/items_categorias/' . $cat->id) }}"
                                            style="margin:0;" class="delete-cat-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-id="{{ $cat->id }}"
                                                data-name="{{ addslashes($cat->nombre) }}" class="delete-btn">
                                                <span class="material-icons-sharp">delete</span>
                                                Eliminar
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No hay categorías registradas.</p>
                    @endif
                </div>

                <!-- Modal para crear categoría -->
                <div id="categoria-add-modal" class="mesa-modal" style="display:none;">
                    <div class="modal-content">
                        <button id="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                        <div class="modal-header" style="margin-bottom:1rem;">
                            <span class="modal-icon material-icons-sharp">category</span>
                            <h2 style="margin:0; font-size:1.25rem;"class="label-dark">Crear Categoría</h2>
                        </div>
                        <form id="cat-add-form" method="POST" action="{{ url('/items_categorias') }}">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="add-nombre" class="label-dark">Nombre</label>
                                    <input id="add-nombre" name="nombre" required placeholder="Ej: Entradas"
                                        autocomplete="off"
                                        style="padding:0.6rem; border:1px solid #e2e8f0; border-radius:6px;">
                                </div>
                                <div class="input-group">
                                    <label for="add-descripcion" class="label-dark">Descripción</label>
                                    <textarea id="add-descripcion" name="descripcion" rows="3" placeholder="Opcional"
                                        style="padding:0.6rem; border:1px solid #e2e8f0; border-radius:6px;"></textarea>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="cancel-modal">Cancelar</button>
                                    <button type="submit" class="button-Add">Agregar Categoría</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal para editar categoría -->
                <div id="categoria-edit-modal" class="mesa-modal" style="display:none;">
                    <div class="modal-content">
                        <button id="close-edit-modal" class="close-modal">
                            <span class="material-icons-sharp">close</span>
                        </button>
                        <div class="modal-header" style="margin-bottom:1rem;">
                            <span class="modal-icon material-icons-sharp">edit</span>
                            <h2 class="label-dark">Editar Categoría</h2>
                        </div>
                        <form id="cat-edit-form" method="POST" action="">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="edit-nombre" class="label-dark">Nombre</label>
                                    <input id="edit-nombre" name="nombre" required placeholder="Ej: Entradas"
                                        autocomplete="off"
                                        style="padding:0.6rem; border:1px solid #e2e8f0; border-radius:6px;">
                                </div>
                                <div class="input-group">
                                    <label for="edit-descripcion" class="label-dark">Descripción</label>
                                    <textarea id="edit-descripcion" name="descripcion" rows="3" placeholder="Opcional"
                                        style="padding:0.6rem; border:1px solid #e2e8f0; border-radius:6px;"></textarea>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="cancel-edit-modal">Cancelar</button>
                                    <button type="submit" class="button-Add">Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal de confirmación de eliminación -->
                <div id="delete-confirm-modal" class="mesa-modal" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="modal-icon material-icons-sharp" style="color:#f59e0b;">warning</span>
                            <h2 style="margin:0;" class="label-dark">Confirmar eliminación</h2>
                        </div>
                        <div style="padding:0.5rem 0 1rem 0;">
                            <p id="delete-confirm-message">¿Desea eliminar esta categoría?</p>
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:0.6rem; margin-top:1.2rem;">
                            <button type="button" id="delete-cancel" class="button-Add edit-btn">Cancelar</button>
                            <button type="button" id="delete-confirm-yes"
                                class="button-Add delete-btn">Eliminar</button>
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
        const newCategoryBtn = document.getElementById('new-category-btn');
        const addModal = document.getElementById('categoria-add-modal');
        const closeAddBtn = document.getElementById('close-modal');
        const cancelAddBtn = document.getElementById('cancel-modal');
        const catAddForm = document.getElementById('cat-add-form');
        const addNombre = document.getElementById('add-nombre');

        // delete confirm modal refs
        const deleteConfirmModal = document.getElementById('delete-confirm-modal');
        const deleteConfirmMessage = document.getElementById('delete-confirm-message');
        const deleteConfirmYes = document.getElementById('delete-confirm-yes');
        const deleteConfirmCancel = document.getElementById('delete-cancel');

        function showAddModal() {
            if (!addModal) return;
            addModal.style.display = 'flex';
            addModal.setAttribute('aria-hidden', 'false');
            if (addNombre) addNombre.focus();
        }

        function hideAddModal() {
            if (!addModal) return;
            addModal.style.display = 'none';
            addModal.setAttribute('aria-hidden', 'true');
            if (catAddForm) catAddForm.reset();
        }

        if (newCategoryBtn) newCategoryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showAddModal();
        });
        if (closeAddBtn) closeAddBtn.addEventListener('click', hideAddModal);
        if (cancelAddBtn) cancelAddBtn.addEventListener('click', hideAddModal);
        if (addModal) addModal.addEventListener('click', function(e) {
            if (e.target === addModal) hideAddModal();
        });


        // Intercept delete buttons: show confirmation modal with category name
        document.querySelectorAll('button.delete-btn[data-id]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const id = this.dataset.id;
                const name = this.dataset.name || '';
                if (!id) return;

                const token = document.querySelector('meta[name="csrf-token"]').content;

                // show initial confirmation with category name
                deleteConfirmMessage.textContent = `¿Desea eliminar la categoría "${name}"?`;
                deleteConfirmModal.style.display = 'flex';

                // cancel handler
                deleteConfirmCancel.onclick = function() {
                    deleteConfirmModal.style.display = 'none';
                };

                // ensure no duplicate handlers
                deleteConfirmYes.onclick = null;

                // first-step delete (no confirm flag)
                deleteConfirmYes.onclick = function() {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    fd.append('_token', token);

                    fetch('/items_categorias/' + id, {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(json => {

                            if (json.in_use) {
                                deleteConfirmMessage.textContent =
                                    `La categoría "${name}" está siendo usada por ${json.count} item(s). ¿Desea eliminarla de todos modos?`;
                                // set final confirm handler
                                deleteConfirmYes.onclick = function() {
                                    const fd2 = new FormData();
                                    fd2.append('_method', 'DELETE');
                                    fd2.append('_token', token);
                                    fd2.append('confirm', '1');

                                    fetch('/items_categorias/' + id, {
                                            method: 'POST',
                                            body: fd2,
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(r2 => r2.json())
                                        .then(j2 => {
                                            if (j2 && j2.success) {
                                                location.reload();
                                            } else {
                                                alert('Error al eliminar la categoría');
                                            }
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            alert('Error al eliminar la categoría');
                                        });
                                };
                            } else if (json.success) {
                                location.reload();
                            } else {
                                // fallback
                                location.reload();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error al eliminar la categoría');
                        });
                };
            });
        });

        // Edit modal logic
        const editModal = document.getElementById('categoria-edit-modal');
        const closeEditBtn = document.getElementById('close-edit-modal');
        const cancelEditBtn = document.getElementById('cancel-edit-modal');
        const catEditForm = document.getElementById('cat-edit-form');
        const editNombre = document.getElementById('edit-nombre');
        const editDescripcion = document.getElementById('edit-descripcion');

        window.openEditCategory = function(id, nombre, descripcion) {
            if (!editModal) return;
            // populate fields
            editNombre.value = nombre || '';
            editDescripcion.value = descripcion || '';
            catEditForm.action = '/items_categorias/' + id;

            editModal.style.display = 'flex';
            editModal.setAttribute('aria-hidden', 'false');
            editNombre.focus();
        }

        function hideEditModal() {
            if (!editModal) return;
            editModal.style.display = 'none';
            editModal.setAttribute('aria-hidden', 'true');
            if (catEditForm) catEditForm.reset();
        }

        if (closeEditBtn) closeEditBtn.addEventListener('click', hideEditModal);
        if (cancelEditBtn) cancelEditBtn.addEventListener('click', hideEditModal);
        if (editModal) editModal.addEventListener('click', function(e) {
            if (e.target === editModal) hideEditModal();
        });

        // submit edit form via AJAX
        if (catEditForm) {
            catEditForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = this.action;
                const formData = new FormData(this);
                // method spoof already present in hidden input
                const token = document.querySelector('meta[name="csrf-token"]').content;

                fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(json => {
                        if (json && json.success) {
                            // update card in DOM
                            const cat = json.data;
                            const card = document.querySelector(`.menu-card[data-id="${cat.id}"]`);
                            if (card) {
                                const h3 = card.querySelector('.card-header h3') || card.querySelector('h3');
                                if (h3) h3.textContent = cat.nombre;
                                const desc = card.querySelector('.description');
                                if (desc) desc.textContent = cat.descripcion || 'Sin descripción';
                            }
                            hideEditModal();
                        } else {
                            alert(json.message || 'Error al actualizar la categoría');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error al actualizar la categoría');
                    });
            });
        }
    </script>

</body>

</html>
