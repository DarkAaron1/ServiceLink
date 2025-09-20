<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de Usuarios - Liquid Glass</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg1:#74ebd5;
            --bg2:#ACB6E5;
            --glass-bg: rgba(255,255,255,0.12);
            --glass-border: rgba(255,255,255,0.16);
            --card-radius: 16px;
        }
        body{
            margin:0;
            min-height:100vh;
            font-family:'Montserrat',sans-serif;
            background: linear-gradient(135deg,var(--bg1) 0%, var(--bg2) 100%);
            display:flex;
            justify-content:center;
            padding:2rem;
            box-sizing:border-box;
        }
        .container{
            width:100%;
            max-width:1100px;
            display:grid;
            grid-template-columns: 1fr 420px;
            gap:1.2rem;
        }

        /* responsive stack */
        @media (max-width:900px){
            .container{ grid-template-columns: 1fr; padding-bottom:2rem; }
        }

        .panel{
            background: var(--glass-bg);
            border-radius: var(--card-radius);
            border:1px solid var(--glass-border);
            box-shadow: 0 8px 32px rgba(31,38,135,0.25);
            padding:1.2rem;
            color:#fff;
        }

        h2{ margin:0 0 1rem 0; font-size:1.25rem; }

        /* tabla */
        table{ width:100%; border-collapse:collapse; color:#fff; }
        th, td{ padding:0.6rem 0.5rem; text-align:left; font-size:0.95rem; border-bottom:1px solid rgba(255,255,255,0.04); }
        th{ opacity:0.9; font-weight:700; font-size:0.9rem; }

        .badge-enabled{ background: linear-gradient(90deg,#5EE7A6,#2AC1A0); color:#063; padding:6px 10px; border-radius:999px; font-weight:700; display:inline-block; }
        .badge-disabled{ background: linear-gradient(90deg,#FFA07A,#FF7A7A); color:#3b0a0a; padding:6px 10px; border-radius:999px; font-weight:700; display:inline-block; }

        /* form */
        .form-row{ display:flex; gap:0.6rem; margin-bottom:0.7rem; }
        .form-row .field{ flex:1; display:flex; flex-direction:column; }
        label{ font-size:0.9rem; margin-bottom:0.25rem; color:rgba(255,255,255,0.9); }
        input, select{ padding:0.6rem 0.8rem; border-radius:10px; border:none; outline:none; background:rgba(255,255,255,0.12); color:#fff; }
        .submit-btn{ width:100%; padding:0.7rem; border-radius:10px; border:none; background:linear-gradient(90deg,var(--bg1),var(--bg2)); color:#063; font-weight:800; cursor:pointer; margin-top:0.4rem; }
        .small-btn{ padding:0.4rem 0.6rem; border-radius:8px; border:none; cursor:pointer; font-weight:700; }

        .actions{ display:flex; gap:0.5rem; align-items:center; }

        .notice{ margin-bottom:0.8rem; color:rgba(255,255,255,0.9); }

        /* table scroll on small */
        .table-wrap{ overflow:auto; max-height:60vh; }

        .msg{ margin-bottom:0.8rem; padding:.6rem; border-radius:10px; background:rgba(0,0,0,0.18); color:#fff; }
    </style>
</head>
<body>
    <div class="container">
        <!-- listado -->
        <div class="panel">
            <h2>Usuarios registrados</h2>

            @if(session('success'))
                <div class="msg">test</div>
            @endif

            <div class="notice">Lista de usuarios. Usa el botón para habilitar / inhabilitar.</div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Run</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                            <tr>
                                <td>{{ $u->run }}</td>
                                <td>{{ $u->username }}</td>
                                <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->estado)
                                        <span class="badge-enabled">Habilitado</span>
                                    @else
                                        <span class="badge-disabled">Inhabilitado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('usuarios.update', $u) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="toggle_estado" value="1">
                                            <button type="submit" class="small-btn" style="background:rgba(255,255,255,0.08); color:#fff;">
                                                {{ $u->estado ? 'Deshabilitar' : 'Habilitar' }}
                                            </button>
                                        </form>

                                        <form method="GET" action="{{ route('usuarios.show', $u) }}">
                                            <button type="submit" class="small-btn" style="background:rgba(255,255,255,0.06); color:#fff;">Ver</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No hay usuarios aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- creación -->
        <div class="panel">
            <h2>Crear nuevo usuario</h2>

            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf

                <div class="form-row">
                    <div class="field">
                        <label for="run">Run</label>
                        <input id="run" name="run" value="{{ old('run') }}" required>
                        @error('run') <small style="color:#ffb3b3">{{ $message }}</small> @enderror
                    </div>
                    <div class="field">
                        <label for="username">Usuario</label>
                        <input id="username" name="username" value="{{ old('username') }}" required>
                        @error('username') <small style="color:#ffb3b3">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="field">
                        <label for="apellido">Apellido</label>
                        <input id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="field">
                        <label for="nacimiento">Fecha nac.</label>
                        <input id="nacimiento" name="nacimiento" type="date" value="{{ old('nacimiento') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" value="{{ old('telefono') }}">
                    </div>
                    <div class="field">
                        <label for="estado">Habilitado</label>
                        <select id="estado" name="estado">
                            <option value="">Inhabilitado</option>
                            <option value="on">Habilitado</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" required>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required>
                    </div>
                </div>

                <button class="submit-btn" type="submit">Crear usuario</button>
            </form>
        </div>
    </div>
</body>
</html>