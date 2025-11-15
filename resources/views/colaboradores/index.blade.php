<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css')}}">
    <title>ServiceLink - Colaboradores</title>
    <style>
        /* pequeños ajustes para tabla/modal */
        .recent-orders table { width:100%; border-collapse:collapse; }
        .recent-orders th, .recent-orders td { padding:.5rem; border-bottom:1px solid #eaeaea; text-align:left; }
        .actions button { margin-right:.25rem; }
        .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:60; }
        .modal.open { display:flex; }
        .modal-content { background:#fff; padding:1rem; width:95%; max-width:720px; border-radius:6px; }
        .grid { display:grid; gap:.5rem; grid-template-columns:repeat(2,1fr); }
        .error { color:crimson; font-size:.9rem; }
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
                <a href="{{ route('index') }}" class="active">
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

        <!-- Main Content: reemplazado por gestión de Colaboradores -->
        <main>
            <h1>Colaboradores</h1>

            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                <div>
                    <p class="text-muted">Gestión de empleados / colaboradores</p>
                </div>
                <div>
                    <button id="open-modal" class="primary">Nuevo Colaborador</button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            <div class="recent-orders">
                <h2>Lista de Colaboradores</h2>
                <table>
                    <thead>
                        <tr>
                            <th>RUT</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Estado</th>
                            <th>Restaurante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($empleados as $e)
                            <tr data-rut="{{ $e->rut }}">
                                <td>{{ $e->rut }}</td>
                                <td>{{ $e->nombre }}</td>
                                <td>{{ $e->apellido }}</td>
                                <td>{{ $e->email }}</td>
                                <td>{{ $e->cargo }}</td>
                                <td>{{ $e->estado }}</td>
                                <td>{{ $e->restaurante_id ?? '-' }}</td>
                                <td class="actions">
                                    <button type="button" class="edit-btn" data-employee='@json($e)'>Editar</button>
                                    <button type="button" class="delete-btn" data-rut="{{ $e->rut }}">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End Main Content -->

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
                            <p>Bienvenido, <b>{{ $usuario->nombre ?? 'Usuario' }}</b></p>
                            <small class="text-muted">{{ $rolName ?? 'Admin' }}</small>
                        </div>
                        <!--div class="profile-photo">
                            <img src="{{ asset('favicon.ico') }}">
                        </div-->
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

    <!-- Modal reutilizable para crear/editar -->
<div id="modal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true">
        <h2 id="modal-title">Nuevo Colaborador</h2>
        <form id="employee-form" method="POST" action="{{ route('empleados.store') }}">
            @csrf
            <!-- method override se inyecta dinámicamente para editar -->
            <div class="grid">
                <div>
                    <label>RUT</label>
                    <input name="rut" required>
                    @error('rut') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Nombre</label>
                    <input name="nombre" required>
                    @error('nombre') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Apellido</label>
                    <input name="apellido" required>
                    @error('apellido') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" required>
                    @error('fecha_nacimiento') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Teléfono</label>
                    <input name="fono" required>
                    @error('fono') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password">
                    <small class="text-muted">Dejar vacío si no desea cambiar la contraseña (solo edición)</small>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Confirmar Password</label>
                    <input type="password" name="password_confirmation">
                </div>
                <div>
                    <label>Cargo (rol)</label>
                    <select name="cargo" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->nombre }}">{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                    @error('cargo') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Estado</label>
                    <select name="estado">
                        <option value="activo">activo</option>
                        <option value="inactivo">inactivo</option>
                    </select>
                </div>
                <div style="grid-column:1 / -1">
                    <label>Restaurante</label>
                    <select name="restaurante_id" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($restaurantes as $res)
                            <option value="{{ $res->id }}">{{ $res->nombre }}</option>
                        @endforeach
                    </select>
                    @error('restaurante_id') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="margin-top:1rem; display:flex; gap:.5rem; justify-content:flex-end;">
                <button type="submit" class="primary" id="submit-btn">Guardar</button>
                <button type="button" id="close-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
	// helper
	const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

	// modal & form elements
	const modal = document.getElementById('modal');
	const openBtn = document.getElementById('open-modal');
	const closeBtn = document.getElementById('close-modal');
	const form = document.getElementById('employee-form');
	const modalTitle = document.getElementById('modal-title');

	// abrir modal en modo crear
	openBtn && openBtn.addEventListener('click', () => {
		modal.classList.add('open');
		modalTitle.textContent = 'Nuevo Colaborador';
		form.reset();
		form.action = "{{ route('empleados.store') }}";
		// eliminar método spoof si existe
		const m = form.querySelector('input[name="_method"]');
		if (m) m.remove();
		// rut editable
		form.querySelector('[name="rut"]').removeAttribute('readonly');
	});

	// cerrar
	closeBtn && closeBtn.addEventListener('click', () => modal.classList.remove('open'));

	// abrir modal en modo editar -> llenar campos
	document.querySelectorAll('.edit-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			const emp = JSON.parse(btn.getAttribute('data-employee'));
			modal.classList.add('open');
			modalTitle.textContent = 'Editar Colaborador';

			// poblar campos
			form.querySelector('[name="rut"]').value = emp.rut ?? '';
			form.querySelector('[name="nombre"]').value = emp.nombre ?? '';
			form.querySelector('[name="apellido"]').value = emp.apellido ?? '';
			form.querySelector('[name="fecha_nacimiento"]').value = emp.fecha_nacimiento ?? '';
			form.querySelector('[name="fono"]').value = emp.fono ?? '';
			form.querySelector('[name="email"]').value = emp.email ?? '';
			form.querySelector('[name="cargo"]').value = emp.cargo ?? '';
			form.querySelector('[name="estado"]').value = emp.estado ?? 'inactivo';
			form.querySelector('[name="restaurante_id"]').value = emp.restaurante_id ?? '';

			// para edición no requerimos password (vacío)
			form.querySelector('[name="password"]').value = '';
			form.querySelector('[name="password_confirmation"]').value = '';

			// ajustar acción para PATCH a un endpoint identificable (ajustar backend si es necesario)
			form.action = '/colaboradores/' + encodeURIComponent(emp.rut);

			// agregar spoof method PATCH si no existe
			if (!form.querySelector('input[name="_method"]')) {
				const methodInput = document.createElement('input');
				methodInput.type = 'hidden';
				methodInput.name = '_method';
				methodInput.value = 'PATCH';
				form.appendChild(methodInput);
			} else {
				form.querySelector('input[name="_method"]').value = 'PATCH';
			}

			// rut no editable para edición
			form.querySelector('[name="rut"]').setAttribute('readonly', 'readonly');
		});
	});

	// eliminar tramite fetch DELETE
	document.querySelectorAll('.delete-btn').forEach(btn => {
		btn.addEventListener('click', async () => {
			const rut = btn.getAttribute('data-rut');
			if (!confirm('Eliminar colaborador ' + rut + ' ?')) return;
			try {
				const res = await fetch('/colaboradores/' + encodeURIComponent(rut), {
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': csrfToken,
						'Accept': 'application/json'
					}
				});
				if (!res.ok) throw new Error('Error al eliminar');
				// remover fila de la tabla
				const row = document.querySelector('tr[data-rut="' + rut + '"]');
				row && row.remove();
			} catch (err) {
				alert('No se pudo eliminar: ' + err.message);
			}
		});
	});

	// abrir modal automáticamente si hay errores desde la validación del servidor
	@if($errors->any())
		modal.classList.add('open');
		modalTitle.textContent = 'Error - Revisar datos';
	@endif
</script>

</body>

</html>