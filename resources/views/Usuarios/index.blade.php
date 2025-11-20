<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css')}}">
    <title>ServiceLink - Usuarios</title>
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
                <a href="#" class="active">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="{{ route('usuarios.index') }}">
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
                <a href="{{ route('comandas.index', ['open' => 'true']) }}">
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
                <a href="#">
                    <span class="material-icons-sharp">
                        table_restaurant
                    </span>
                    <h3>Mesas</h3>
                </a>
                <a href="{{ route('usuarios.index') }}">
                    <span class="material-icons-sharp">
                        person
                    </span>
                    <h3>Usuarios</h3>
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
           
                 
            <!-- End of Analyses -->

            <!-- New Users Section -->
           <div class="new-users">
                         <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                             <h2 style="margin: 0;">Usuarios</h2>
                             <a href="#" id="new-user-btn" style="background-color: #007bff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">+ Nuevo Usuario</a>
                         </div>

                         <!-- Modal overlay (hidden) -->
                         <div id="modal-overlay" style="display:none; position: fixed; inset:0; background: rgba(0,0,0,0.45); z-index: 9999; align-items: center; justify-content: center;">
                            <div id="modal" role="dialog" aria-modal="true" style="background: #fff; width: 90%; max-width: 820px; margin: 0 auto; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); overflow: hidden;">
                                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #eee; background:#fafafa;">
                                    <h3 style="margin:0;">Crear Nuevo Usuario</h3>
                                    <button id="close-modal" aria-label="Cerrar" style="background:transparent; border:none; font-size:18px; cursor:pointer;">✖</button>
                                </div>
                                          <div style="padding:16px;">
                                              @include('Usuarios._form', ['action' => route('usuarios.store'), 'method' => 'POST', 'usuario' => null, 'inModal' => true])
                                          </div>
                            </div>
                         </div>

                        @if(session('success'))
                            <div style="background:#d4edda; border:1px solid #c3e6cb; padding:10px; border-radius:4px; margin-top:12px;">
                                {{ session('success') }}
                            </div>
                        @endif

                         <div class="user-list" style="margin-top: 20px;">
             @foreach($usuarios as $usuario)
            <div class="user">
                <h2>{{ $usuario->nombre }} {{ $usuario->apellido }}</h2>
                <p>Correo: {{ $usuario->email }}</p>
                <p>Rol: {{ $usuario->rol_id }}</p>
                <p>Estado: {{ $usuario->estado }}</p>
                <p>Creado: {{ \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y') }}</p>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="{{ route('usuarios.edit', $usuario->rut) }}" style="background-color: #ffc107; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 12px;">✏️ Editar</a>
                    <form action="{{ route('usuarios.destroy', $usuario->rut) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-weight: bold; font-size: 12px; cursor: pointer;" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">🗑️ Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            
            <!-- End of Recent Orders -->

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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('new-user-btn');
            var overlay = document.getElementById('modal-overlay');
            var closeBtn = document.getElementById('close-modal');
            function openModal() {
                if (!overlay) return;
                overlay.style.display = 'flex';
                // focus first input
                var first = overlay.querySelector('input, select, textarea');
                if (first) first.focus();
            }
            function closeModal() {
                if (!overlay) return;
                overlay.style.display = 'none';
            }

            if (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    openModal();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeModal();
                });
            }

            // Cancel button inside form (when inModal)
            document.addEventListener('click', function (e) {
                if (e.target && e.target.id === 'cancel-new-user') {
                    e.preventDefault();
                    closeModal();
                }
            });

            // click outside modal to close
            if (overlay) {
                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) closeModal();
                });
            }

            // Esc key to close
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });
        });
    </script>
    <script src="{{ asset('index.js') }}"></script>
</body>


</html>