<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-tables.css') }}">
    <title>ServiceLink - Colaboradores</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .modal.open {
            display: flex;
        }

        .modal-content {
            background: #fff;
            padding: 2rem 2.5rem;
            width: auto;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
            animation: modalFadeIn 0.3s;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            margin: auto;
        }

        .grid {
            display: grid;
            gap: .5rem;
            grid-template-columns: repeat(2, 1fr);
        }

        .error {
            color: crimson;
            font-size: .9rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .input-group label {
            font-weight: 500;
            color: #444;
        }

        .input-group input,
        .input-group select {
            padding: 0.65rem 0.9rem;
            border: 1.5px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            background: #fafbfc;
            outline: none;
            transition: border-color 0.2s;
            width: 250px;
            height: 40px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #1976d2;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .form-actions {
            display: flex;
            gap: 0.7rem;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }

        .form-actions button {
            padding: 0.6rem 1.1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        #close-modal {
            border: 1.5px solid #bdbdbd;
            background: #fff;
            color: #555;
        }

        #close-modal:hover {
            background: #f5f5f5;
        }

        .form-actions button[type="submit"] {
            border: none;
            background: #1976d2;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08);
        }

        .form-actions button[type="submit"]:hover {
            background: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.15);
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.2rem;
        }

        .modal-header h2 {
            margin: 0;
            font-weight: 600;
            color: #222;
            font-size: 1.35rem;
        }

        .modal-icon {
            font-size: 2.2rem;
            color: #1976d2;
            background: #e3f2fd;
            border-radius: 50%;
            padding: 0.4rem;
        }

        #modal {
            animation: modalFadeIn 0.3s;
        }

        /* Modal de confirmación de eliminación: centrar en pantalla */
        .mesa-modal-eliminar {
            display: none;
            position: fixed;
            inset: 0;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1200;
            padding: 1rem;
            box-sizing: border-box;
        }

        .mesa-modal-eliminar[style*="display:flex"] {
            display: flex;
        }

        .modal-content-eliminar {
            width: 480px;
            max-width: 95%;
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <script src="{{ asset('index.js') }}"></script>
    <div class="container">
        <!-- Sidebar Section -->
        @include('partials.sidebar')
        <!-- End of Sidebar Section -->

        <!-- Main Content: reemplazado por gestión de Colaboradores -->
        <main>
            <h1>Gestión de Colaboradores</h1>
            @php $role = session('empleado_cargo') ?? (session('usuario_nombre') ? 'Usuario' : null); @endphp

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables">
                <div class="header" style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                    @if (in_array($role, ['Administrador', 'Usuario']))
                        <button id="open-modal" class="btn-primary button-Add">
                            <span class="material-icons-sharp" style="font-size:1.3rem;">add</span>
                            Nuevo Colaborador
                        </button>
                    @endif
                </div>

                <div class="colaboradores-grid">
                    @if (isset($empleados) && $empleados->count())
                        @foreach ($empleados as $e)
                            <div class="colaborador-card" data-rut="{{ $e->rut }}">
                                <div class="icon">
                                    <span class="material-icons-sharp">person</span>
                                </div>
                                <h3>{{ $e->nombre }} {{ $e->apellido }}</h3>
                                <div class="colaborador-info">
                                    <p><strong>{{ $e->cargo }}</strong></p>
                                    <p>{{ $e->email }}</p>
                                </div>
                                <div class="colaborador-estado {{ strtolower($e->estado) }}">
                                    {{ $e->estado }}
                                </div>
                                <div class="mesa-actions" style="margin-top: auto; padding-top: 1rem;">
                                    <button class="mesa-btn edit" type="button"
                                        data-employee='@json($e)'>
                                        <span class="material-icons-sharp">edit</span>
                                    </button>
                                    @if (in_array($role, ['Administrador', 'Usuario']))
                                        <button class="mesa-btn delete" type="button" data-rut="{{ $e->rut }}">
                                            <span class="material-icons-sharp">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No hay colaboradores registrados.</p>
                    @endif
                </div>
            </div>
        </main>
        <!-- End Main Content -->

        <!-- Right Section -->
        @include('partials.right-section')

    </div>

    </div>

    <!-- Modal reutilizable para crear/editar -->
    <div id="modal" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true">
            <button id="close-modal"
                style="position: absolute; right: 1rem; top: 1rem; background: transparent; border: none; font-size: 1.5rem; color: #888; cursor: pointer;">
                <span class="material-icons-sharp">close</span>
            </button>
            <div class="modal-header">
                <span class="modal-icon material-icons-sharp">person</span>
                <h2 id="modal-title" class="label-dark">Nuevo Colaborador</h2>
            </div>
            <form id="employee-form" method="POST" action="{{ route('empleados.store') }}">
                @csrf
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">
                    <div class="form-column">
                        <div class="input-group">
                            <label for="rut" class="label-dark">RUT</label>
                            <input id="rut" name="rut" required placeholder="Ej: 12.345.678-9" maxlength="12"
                                autocomplete="off" pattern="^[0-9\.\-]{1,15}-[0-9Kk]$"
                                title="Formato RUT: 12.345.678-9. Solo números, puntos, guión y la letra K"
                                inputmode="text">
                            @error('rut')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="nombre"class="label-dark">Nombre</label>
                            <input id="nombre" name="nombre" type="text" required placeholder="Ej: Juan"
                                autocomplete="off" pattern="[A-Za-zÁÉÍÓÚáéíóúÜüÑñ\s]{1,100}"
                                title="Solo letras y espacios">
                            @error('nombre')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="apellido" class="label-dark">Apellido</label>
                            <input id="apellido" type="text" name="apellido" required placeholder="Ej: Pérez"
                                autocomplete="off" pattern="[A-Za-zÁÉÍÓÚáéíóúÜüÑñ\s]{1,100}"
                                title="Solo letras y espacios">
                            @error('apellido')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="email" class="label-dark">Email</label>
                            <input id="email" type="email" name="email" required
                                placeholder="Ej: juan@example.com" autocomplete="off">
                            @error('email')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="input-group">
                            <label for="fono" class="label-dark">Teléfono</label>
                            <input id="fono" name="fono" maxlength="15" required placeholder="Ej: 912345678"
                                autocomplete="off" inputmode="numeric" pattern="[0-9]{7,15}"
                                title="Solo números (7-15 dígitos)">
                            @error('fono')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="fecha_nacimiento" class="label-dark">Fecha Nacimiento</label>
                            <input id="fecha_nacimiento" type="date" name="fecha_nacimiento" required>
                            @error('fecha_nacimiento')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="cargo" class="label-dark">Cargo</label>
                            <select id="cargo" name="cargo" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->nombre }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                            @error('cargo')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group">
                            <label for="estado" class="label-dark">Estado</label>
                            <select id="estado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Guardar</button>
                    <button type="button" id="close-modal-btn">Cancelar</button>
                    <button type="button" id="reset-password-btn">Restablecer Contraseña</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div id="delete-confirm-modal" class="mesa-modal-eliminar">
        <div class="modal-content-eliminar">
            <div class="modal-header-eliminar">
                <span class="modal-icon material-icons-sharp" style="color:#f59e0b;">warning</span>
                <h2 style="margin:0;" class="label-dark">Confirmar eliminación</h2>
            </div>
            <div style="padding:0.5rem 0 1rem 0;">
                <p id="delete-confirm-message">¿Desea eliminar este Usuario?</p>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.6rem; margin-top:1.2rem;">
                <button type="button" id="delete-cancel" class="button-Add edit-btn">Cancelar</button>
                <button type="button" id="delete-confirm-yes" class="button-Add delete-btn">Eliminar</button>
            </div>
        </div>
    </div>


    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const modal = document.getElementById('modal');
        const openBtn = document.getElementById('open-modal');
        const closeBtnTop = document.querySelector('#close-modal');
        const closeBtnBottom = document.getElementById('close-modal-btn');
        const form = document.getElementById('employee-form');
        const modalTitle = document.getElementById('modal-title');
        const rutInput = form.querySelector('#rut');
        const resetPasswordBtn = document.getElementById('reset-password-btn');

        //Función para reestablecer contraseña

        resetPasswordBtn.addEventListener('click', async () => {
            const rut = rutInput.value.trim();

            // Validación simple
            if (!rut) {
                alert('Por favor, ingrese el RUT del colaborador.');
                return;
            }

            // Feedback visual (opcional: cambiar texto del botón)
            const originalText = resetPasswordBtn.innerText;
            resetPasswordBtn.innerText = 'Enviando...';
            resetPasswordBtn.disabled = true;

            try {
                const res = await fetch('/colaboradores/' + encodeURIComponent(rut) + '/reset-password', {
                    method: 'POST', // Coincide con Route::post
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await res.json(); // Leemos la respuesta JSON del controlador

                if (!res.ok) {
                    // Lanzamos error con el mensaje que viene del servidor
                    throw new Error(data.message || 'Error desconocido');
                }

                alert(data.message); // "Se envió un correo con un enlace para establecer la contraseña."
                // si existe función closeModal, cerramos modal
                if (typeof closeModal === 'function') closeModal();

            } catch (err) {
                alert('Atención: ' + err.message);
            } finally {
                // Restaurar botón
                resetPasswordBtn.innerText = originalText;
                resetPasswordBtn.disabled = false;
            }
        });

        // Función para formatear RUT
        function formatRUT(rut) {
            // Eliminar caracteres no numéricos excepto guión
            rut = rut.replace(/[^\d\-kK]/g, '').toUpperCase();

            // Si no tiene guión, agregarlo
            if (!rut.includes('-')) {
                if (rut.length > 1) {
                    const body = rut.slice(0, -1);
                    const dv = rut.slice(-1);
                    rut = body + '-' + dv;
                }
            }

            return rut;
        }

        // Validar formato del RUT
        function isValidRUTFormat(rut) {
            return /^\d{1,2}\.\d{3}\.\d{3}-[0-9kK]$/.test(rut) || /^\d{1,8}-[0-9kK]$/.test(rut);
        }

        // Event listener para autoformato de RUT al perder el foco
        rutInput.addEventListener('blur', (e) => {
            let value = e.target.value.trim();

            if (!value) return; // No formatear si está vacío

            value = formatRUT(value);

            // Agregar puntos cada 3 dígitos
            if (value.includes('-')) {
                const partes = value.split('-');
                const numeros = partes[0];
                const dv = partes[1];
                value = numeros.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + dv;
            }

            e.target.value = value;
        });

        // Sanitizar inputs: permitir solo letras en nombre/apellido y solo dígitos en fono
        function allowOnlyLetters(el) {
            el.addEventListener('input', function() {
                const pos = this.selectionStart;
                // Permite letras (incluye acentos y ñ), espacios, guiones y apóstrofes
                this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÜüÑñ\s\-']/g, '');
                this.setSelectionRange(pos, pos);
            });
        }

        function allowOnlyDigits(el) {
            el.addEventListener('input', function() {
                const pos = this.selectionStart;
                this.value = this.value.replace(/\D/g, '');
                this.setSelectionRange(pos, pos);
            });
        }

        const nombreInput = form.querySelector('#nombre');
        const apellidoInput = form.querySelector('#apellido');
        const fonoInput = form.querySelector('#fono');

        if (nombreInput) allowOnlyLetters(nombreInput);
        if (apellidoInput) allowOnlyLetters(apellidoInput);
        if (fonoInput) allowOnlyDigits(fonoInput);

        // Sanitizar RUT: permitir solo dígitos, puntos, guión y la letra K (mayúscula o minúscula)
        function allowOnlyRut(el) {
            el.addEventListener('input', function() {
                const pos = this.selectionStart;
                // Permite dígitos, puntos, guiones y K/k
                this.value = this.value.replace(/[^0-9\.\-Kk]/g, '').toUpperCase();
                // Si se ingresó más de un guión, mantener solo el último guión
                const hyphens = (this.value.match(/-/g) || []).length;
                if (hyphens > 1) {
                    // eliminar todos los guiones y reinsertar uno antes del último caracter
                    this.value = this.value.replace(/-/g, '');
                    if (this.value.length > 1) {
                        const dv = this.value.slice(-1);
                        const body = this.value.slice(0, -1);
                        this.value = body + '-' + dv;
                    }
                }
                this.setSelectionRange(pos, pos);
            });
        }

        if (rutInput) allowOnlyRut(rutInput);

        // Permitir solo dígitos, puntos, guión y la letra K para RUT (sanitiza en tiempo real)
        function allowOnlyRut(el) {
            el.addEventListener('input', function() {
                const pos = this.selectionStart;
                // permitir números, puntos, guión y k/K
                this.value = this.value.replace(/[^0-9\.\-kK]/g, '').toUpperCase();
                // Si hay más de un guión, dejar solo el último (opcional)
                const parts = this.value.split('-');
                if (parts.length > 2) {
                    const last = parts.pop();
                    this.value = parts.join('') + '-' + last;
                }
                this.setSelectionRange(pos, pos);
            });
        }

        if (rutInput) allowOnlyRut(rutInput);

        function openModal() {
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            const firstInput = form.querySelector('#rut');
            if (firstInput) firstInput.focus();
        }

        function closeModal() {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
            form.reset();
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
        }

        openBtn && openBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Nuevo Colaborador';
            form.action = "{{ route('empleados.store') }}";
            form.querySelector('[name="rut"]').removeAttribute('readonly');
            openModal();
        });

        closeBtnTop && closeBtnTop.addEventListener('click', closeModal);
        closeBtnBottom && closeBtnBottom.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        document.querySelectorAll('.edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const employee = JSON.parse(btn.getAttribute('data-employee'));
                modalTitle.textContent = 'Editar Colaborador';
                form.action = '/colaboradores/' + encodeURIComponent(employee.rut);

                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                form.querySelector('[name="rut"]').value = employee.rut;
                form.querySelector('[name="rut"]').setAttribute('readonly', 'readonly');
                form.querySelector('[name="nombre"]').value = employee.nombre;
                form.querySelector('[name="apellido"]').value = employee.apellido;
                form.querySelector('[name="email"]').value = employee.email;
                form.querySelector('[name="fono"]').value = employee.fono;
                form.querySelector('[name="fecha_nacimiento"]').value = employee.fecha_nacimiento;
                form.querySelector('[name="cargo"]').value = employee.cargo;
                form.querySelector('[name="estado"]').value = employee.estado;

                openModal();
            });
        });

        // Obtener referencia al modal
        const deleteModal = document.getElementById('delete-confirm-modal');
        const deleteMessage = document.getElementById('delete-confirm-message');
        const deleteCancel = document.getElementById('delete-cancel');
        const deleteConfirm = document.getElementById('delete-confirm-yes');

        let rutToDelete = null; // Guarda el RUT del colaborador a eliminar

        // Mostrar modal en vez del confirm()
        document.querySelectorAll('.delete').forEach(btn => {
            btn.addEventListener('click', () => {
                rutToDelete = btn.getAttribute('data-rut');

                // Mensaje dentro del modal
                deleteMessage.textContent = `¿Deseas eliminar al colaborador con RUT ${rutToDelete}?`;

                // Mostrar el modal (usar flex para centrar)
                deleteModal.style.display = 'flex';
            });
        });

        // Botón CANCELAR del modal
        deleteCancel.addEventListener('click', () => {
            deleteModal.style.display = 'none';
            rutToDelete = null;
        });

        // Botón ELIMINAR del modal

        deleteConfirm.addEventListener('click', async () => {
            if (!rutToDelete) return;

            try {
                const res = await fetch('/colaboradores/' + encodeURIComponent(rutToDelete), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Error al eliminar');

                const card = document.querySelector('.colaborador-card[data-rut="' + rutToDelete + '"]');
                if (card) card.remove();

            } catch (err) {
                alert('No se pudo eliminar: ' + err.message);
            }

            // Cerrar modal
            deleteModal.style.display = 'none';
            rutToDelete = null;
        });


        @if ($errors->any())
            openModal();
            //agrega el error especifico por consola
            console.error(@json($errors->all()));
            modalTitle.textContent = 'Error - Revisar datos';
        @endif

        // Establecer fecha máxima para fecha de nacimiento (18 años atrás)
        const hoy = new Date();
        hoy.setFullYear(hoy.getFullYear() - 18);

        const maxDate = hoy.toISOString().split('T')[0];
        document.getElementById('fecha_nacimiento').max = maxDate;
    </script>


</body>
