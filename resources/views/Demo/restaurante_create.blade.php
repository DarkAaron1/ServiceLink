<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <title>ServiceLink - Crear Restaurante</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        body {
            background: linear-gradient(180deg, #f5f7fb 0%, #ffffff 100%);
            font-family: Poppins;
        }

        .restaurante-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .restaurante-card {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .side-brand {
            background: linear-gradient(135deg, #5b9df9 0%, #3a73d9 100%);
            color: #fff;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.2rem;
        }

        .side-brand .logo {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .side-brand img {
            width: 40px;
            height: 40px;
        }

        .side-brand h2 {
            margin: 0;
            font-size: 1.6rem;
            letter-spacing: 0.4px;
        }

        .side-brand p {
            margin: 0;
            opacity: .95;
            max-width: 18rem;
            line-height: 1.4;
        }

        .restaurante-right {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1rem;
        }

        .form-title {
            margin: 0 0 .5rem 0;
        }

        .field {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: #f6f8fb;
            border-radius: 8px;
            padding: .6rem .8rem;
            border: 1px solid transparent;
            transition: all .15s ease;
        }

        .field:focus-within {
            box-shadow: 0 6px 18px rgba(58, 115, 217, 0.08);
            border-color: rgba(58, 115, 217, 0.18);
            background: #fff;
        }

        .field .material-icons-sharp {
            color: #6b7aa6;
        }

        .field input,
        .field select {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: .98rem;
            padding: .45rem 0;
        }

        .primary {
            font-weight: 500;
            color: #fff;
        }

        .btn-primary {
            background: linear-gradient(90deg, #3a73d9, #5b9df9);
            color: #fff;
            border: none;
            padding: .75rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            box-shadow: 0 6px 18px rgba(58, 115, 217, 0.12);
        }

        .link-muted {
            color: #6b7aa6;
            font-size: .92rem;
            text-decoration: none;
        }

        .small {
            font-size: .9rem;
            color: #6b7aa6;
        }

        .error {
            color: #d0464c;
            font-size: .9rem;
            margin-top: .4rem;
        }

        .success {
            color: #2f8e44;
            font-size: .95rem;
            margin-bottom: 1rem;
            background: #f0f8f5;
            padding: .6rem .8rem;
            border-radius: 8px;
        }

        @media (max-width:900px) {
            .restaurante-card {
                grid-template-columns: 1fr;
            }

            .side-brand {
                padding: 2rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="restaurante-container">
        <div class="restaurante-card">
            <div class="side-brand">
                <div class="logo">
                    <img src="{{ asset('favicon.ico') }}" alt="ServiceLink">
                    <h2>Service<span style="font-weight:700">Link</span></h2>
                </div>
                <p class="primary">Registra un nuevo restaurante en tu plataforma. Configura los datos básicos y asigna
                    un administrador para comenzar.</p>
                <div style="display:flex;flex-direction:column;gap:.6rem;margin-top:1rem;">
                    <div style="display:flex;gap:.6rem;align-items:center;">
                        <span class="material-icons-sharp"
                            style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">storefront</span>
                        <span>Gestión completa</span>
                    </div>
                    <div style="display:flex;gap:.6rem;align-items:center;">
                        <span class="material-icons-sharp"
                            style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">people</span>
                        <span>Control de equipo</span>
                    </div>
                </div>
            </div>

            <div class="restaurante-right">
                <form method="POST" action="{{ route('restaurante.store') }}">
                    @csrf
                    <h1 class="form-title">Crear Restaurante</h1>
                    <p class="small">Completa los datos para registrar un nuevo restaurante</p>

                    @if (session('success'))
                        <div class="success">{{ session('success') }}</div>
                    @endif

                    <!-- Nombre -->
                    <label class="field" for="nombre" style="margin-top:1rem;">
                        <span class="material-icons-sharp">storefront</span>
                        <input id="nombre" type="text" name="nombre" placeholder="Nombre del restaurante"
                            value="{{ old('nombre') }}" required autofocus>
                    </label>
                    @error('nombre')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Dirección -->
                    <label class="field" for="direccion" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">location_on</span>
                        <input id="direccion" type="text" name="direccion" placeholder="Dirección"
                            value="{{ old('direccion') }}" required>
                    </label>
                    @error('direccion')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Teléfono -->
                    <label class="field" for="telefono" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">call</span>
                        <input id="telefono" type="text" name="telefono" placeholder="Teléfono"
                            value="{{ old('telefono') }}" required>
                    </label>
                    @error('telefono')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Email -->
                    <label class="field" for="email" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">mail_outline</span>
                        <input id="email" type="email" name="email" placeholder="Correo electrónico"
                            value="{{ old('email') }}" required>
                    </label>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Fecha de creación -->
                    <label class="field" for="fecha_creacion" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">event</span>
                        <input id="fecha_creacion" type="date" max="<?= date('Y-m-d') ?>" name="fecha_creacion"
                            value="{{ old('fecha_creacion') }}">
                    </label>
                    @error('fecha_creacion')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Administrador -->
                    <label class="field" for="rut_admin" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">person</span>
                        <input id="rut_admin" type="text" name="rut_admin"
                            placeholder="RUT del administrador (ej: 12.345.678-9)" value="{{ old('rut_admin') }}"
                            required>
                    </label>
                    @error('rut_admin')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn-primary" style="margin-top:1rem;">Crear Restaurante</button>

                    <div style="margin-top:1rem;display:flex;justify-content:center;gap:.5rem;align-items:center;">
                        <span class="small">¿Deseas volver?</span>
                        <a href="{{ route('login') }}" class="link-muted" style="font-weight:600;">Ir al panel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('index.js') }}"></script>
    <script>
        // Autoformato para RUT
        (function() {
            const rutInput = document.getElementById('rut_admin');
            if (!rutInput) return;

            rutInput.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/[^\dkK]/g, '').toUpperCase();

                if (value.length > 8) {
                    value = value.slice(0, 8) + '-' + value.slice(8, 9);
                }
                if (value.length > 5) {
                    value = value.slice(0, 5) + '.' + value.slice(5);
                }
                if (value.length > 2) {
                    value = value.slice(0, 2) + '.' + value.slice(2);
                }

                e.target.value = value;
            });
        })();

    </script>
</body>

</html>
