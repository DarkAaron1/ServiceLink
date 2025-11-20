<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css')}}">
    <title>ServiceLink</title>
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
            <h1>Dashboard</h1>
            <!-- Analyses -->
            <div class="analyse">
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Ingresos</h3>
                            <h1>$65,024</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="visits">
                    <div class="status">
                        <div class="info">
                            <h3>Productos Vendidos</h3>
                            <h1>24,981</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Producto Más Vendido</h3>
                            <h1>Churrasco Italiano</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>1,500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">
                <h2>Usuarios Activos</h2>
                <div class="user-list">
                    <div class="user">
                        <!--img src="images/profile-2.jpg"-->
                        <h2>Jack</h2>
                        <p>54 Min Ago</p>
                    </div>
                    <div class="user">
                        <!--img src="images/profile-3.jpg"-->
                        <h2>Amir</h2>
                        <p>3 Hours Ago</p>
                    </div>
                    <div class="user">
                        <!--img src="images/profile-4.jpg"-->
                        <h2>Ember</h2>
                        <p>6 Hours Ago</p>
                    </div>
                    <div class="user">
                        <!--img src="images/plus.png"-->
                        <h2>More</h2>
                        <p>New User</p>
                    </div>
                </div>
            </div>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Ordenes Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Mesa</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <a href="#">Show All</a>
            </div>
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

    <!-- <script src="orders.js"></script> -->
    
</body>

</html>