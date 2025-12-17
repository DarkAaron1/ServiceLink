@php
    $role = session('empleado_cargo') ?? session('usuario_rol') ?? (session('usuario_nombre') ? 'Usuario' : null);
    $rl = strtolower(trim($role ?? ''));
@endphp

<aside>
    <div class="toggle">
        <div class="logo">
            <img src="{{ asset('favicon.ico') }}">
            <h2>Service<span class="primary">Link</span></h2>
        </div>
        <div class="close" id="close-btn">
            <span class="material-icons-sharp">close</span>
        </div>
    </div>

    <div class="sidebar">
        {{-- Links visibles según rol (manteniendo estructura original para estilos) --}}
        @if($rl === 'cocinero')
            <a href="{{ route('cocina.index') }}" class="{{ request()->routeIs('cocina*') ? 'active' : '' }}">
                <span class="material-icons-sharp">restaurant</span>
                <h3>Cocina</h3>
            </a>
        @elseif($rl === 'mesero')
            <a href="{{ route('comandas.index') }}" class="{{ request()->routeIs('comandas*') ? 'active' : '' }}">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>Comandas</h3>
            </a>
        @else
            {{-- Administrador / Usuario / Empleado --}}
            <a href="{{ route('demo.index') }}" class="{{ request()->routeIs('demo*') ? 'active' : '' }}">
                <span class="material-icons-sharp">dashboard</span>
                <h3>Dashboard</h3>
            </a>

            <a href="{{ route('empleados.index') }}" class="{{ request()->routeIs('empleados*') ? 'active' : '' }}">
                <span class="material-icons-sharp">person_outline</span>
                <h3>Colaboradores</h3>
            </a>

            <a href="{{ route('comandas.index') }}" class="{{ request()->routeIs('comandas*') ? 'active' : '' }}">
                <span class="material-icons-sharp">receipt_long</span>
                <h3>Comandas</h3>
            </a>
{{-- 
            <a href="#">
                <span class="material-icons-sharp">insights</span>
                <h3>Estadísticas</h3>
            </a> --}}

            <a href="{{ route('cocina.index') }}" class="{{ request()->routeIs('cocina*') ? 'active' : '' }}">
                <span class="material-icons-sharp">restaurant</span>
                <h3>Cocina</h3>
            </a>

            <a href="{{ route('items_menu.index') }}" class="{{ request()->routeIs('items_menu*') ? 'active' : '' }}">
                <span class="material-icons-sharp">inventory</span>
                <h3>Menú</h3>
            </a>
{{-- 
            <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias*') ? 'active' : '' }}">
                <span class="material-icons-sharp">label</span>
                <h3>Categorías</h3>
            </a> --}}

            <a href="{{ route('mesas.index') }}" class="{{ request()->routeIs('mesas*') ? 'active' : '' }}">
                <span class="material-icons-sharp">table_restaurant</span>
                <h3>Mesas</h3>
            </a>
        @endif

        {{-- Logout / info sesión --}}
        <div class="sidebar-footer" style="margin-top:1.5rem;font-size:.9rem;color:#6b7aa6;">
            @if(session('usuario_nombre'))
                {{-- <div>Sesión: <strong>{{ session('usuario_nombre') }}</strong> (Usuario)</div> --}}
                <div style="margin-top:.5rem;"><a href="{{ route('logout') }}">Cerrar sesión</a></div>
            @elseif(session('empleado_nombre'))
                <div>Sesión: <strong>{{ session('empleado_nombre') }}</strong> ({{ session('empleado_cargo') }})</div>
                <div style="margin-top:.5rem;"><a href="{{ route('logout') }}">Cerrar sesión</a></div>
            @else
                <a href="{{ route('login') }}">Iniciar sesión</a>
            @endif
        </div>
    </div>
</aside>