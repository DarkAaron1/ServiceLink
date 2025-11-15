<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css')}}">
    <link rel="stylesheet" href="{{ asset('style-tables.css')}}">
    <title>ServiceLink - Colaboradores</title>
    <style>
        .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:1000; overflow-y: auto; padding: 1rem 0; }
        .modal.open { display:flex; }
        .modal-content { background:#fff; padding:2rem 2.5rem; width:95%; max-width:400px; border-radius:16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18); animation: modalFadeIn 0.3s; position: relative; max-height: 90vh; overflow-y: auto; margin: auto; }
        .grid { display:grid; gap:.5rem; grid-template-columns:repeat(2,1fr); }
        .error { color:crimson; font-size:.9rem; }
        .input-group { display: flex; flex-direction: column; gap: 0.3rem; }
        .input-group label { font-weight: 500; color: #444; }
        .input-group input,
        .input-group select { padding: 0.65rem 0.9rem; border: 1.5px solid #e0e0e0; border-radius: 6px; font-size: 1rem; background: #fafbfc; outline: none; transition: border-color 0.2s; }
        .input-group input:focus,
        .input-group select:focus { border-color: #1976d2; }
        .form-group { display: flex; flex-direction: column; gap: 1.1rem; }
        .form-actions { display: flex; gap: 0.7rem; justify-content: flex-end; margin-top: 0.5rem; }
        .form-actions button { padding: 0.6rem 1.1rem; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s, color 0.2s; }
        #close-modal { border: 1.5px solid #bdbdbd; background: #fff; color: #555; }
        #close-modal:hover { background: #f5f5f5; }
        .form-actions button[type="submit"] { border: none; background: #1976d2; color: #fff; font-weight: 600; box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08); }
        .form-actions button[type="submit"]:hover { background: #1565c0; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(25, 118, 210, 0.15); }
        .modal-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.2rem; }
        .modal-header h2 { margin: 0; font-weight: 600; color: #222; font-size: 1.35rem; }
        .modal-icon { font-size: 2.2rem; color: #1976d2; background: #e3f2fd; border-radius: 50%; padding: 0.4rem; }
        #modal { animation: modalFadeIn 0.3s; }
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
            <h1>Gestión de Colaboradores</h1>

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            <div style="margin-bottom:2rem;" class="management-tables">
                <div class="header" style="display:flex; align-items:center; justify-content:flex-end; gap:1rem;">
                    <button id="open-modal" class="btn-primary button-Add">
                        <span class="material-icons-sharp" style="font-size:1.3rem;">add</span>
                        Nuevo Colaborador
                    </button>
                </div>

                <div class="colaboradores-grid">
                    @if(isset($empleados) && $empleados->count())
                        @foreach($empleados as $e)
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
                                    <button class="mesa-btn edit" type="button" data-employee='@json($e)'>
                                        <span class="material-icons-sharp">edit</span>
                                    </button>
                                    <button class="mesa-btn delete" type="button" data-rut="{{ $e->rut }}">
                                        <span class="material-icons-sharp">delete</span>
                                    </button>
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
        <button id="close-modal" style="position: absolute; right: 1rem; top: 1rem; background: transparent; border: none; font-size: 1.5rem; color: #888; cursor: pointer;">
            <span class="material-icons-sharp">close</span>
        </button>
        <div class="modal-header">
            <span class="modal-icon material-icons-sharp">person</span>
            <h2 id="modal-title">Nuevo Colaborador</h2>
        </div>
        <form id="employee-form" method="POST" action="{{ route('empleados.store') }}">
            @csrf
            <div class="form-group">
                <div class="input-group">
                    <label for="rut">RUT</label>
                    <input id="rut" name="rut" required placeholder="Ej: 12345678-9" autocomplete="off">
                    @error('rut') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" required placeholder="Ej: Juan" autocomplete="off">
                    @error('nombre') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="apellido">Apellido</label>
                    <input id="apellido" name="apellido" required placeholder="Ej: Pérez" autocomplete="off">
                    @error('apellido') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" required placeholder="Ej: juan@example.com" autocomplete="off">
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="fono">Teléfono</label>
                    <input id="fono" name="fono" required placeholder="Ej: 912345678" autocomplete="off">
                    @error('fono') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="fecha_nacimiento">Fecha Nacimiento</label>
                    <input id="fecha_nacimiento" type="date" name="fecha_nacimiento" required>
                    @error('fecha_nacimiento') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="cargo">Cargo</label>
                    <select id="cargo" name="cargo" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->nombre }}">{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                    @error('cargo') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="input-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" id="close-modal-btn">Cancelar</button>
                    <button type="submit" class="submit-btn">Guardar</button>
                </div>
            </div>
        </form>
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
    
        document.querySelectorAll('.delete').forEach(btn => {
            btn.addEventListener('click', async () => {
                const rut = btn.getAttribute('data-rut');
                if (!confirm('¿Estás seguro de que deseas eliminar este colaborador?')) return;
                try {
                    const res = await fetch('/colaboradores/' + encodeURIComponent(rut), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Error al eliminar');
                    const card = document.querySelector('.colaborador-card[data-rut="' + rut + '"]');
                    card && card.remove();
                } catch (err) {
                    alert('No se pudo eliminar: ' + err.message);
                }
            });
        });
    
        @if($errors->any())
            openModal();
            //agrega el error especifico por consola
            console.error(@json($errors->all()));
            modalTitle.textContent = 'Error - Revisar datos';
        @endif
    </script>
    
    </body>

