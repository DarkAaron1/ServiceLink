<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <title>ServiceLink - Crear Cuenta</title>
    <style>
        /* Estilos específicos del register para mantener la estética sin tocar style.css */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        body {
            background: linear-gradient(180deg, #f5f7fb 0%, #ffffff 100%);
            font-family: Poppins;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-card {
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

        .register-right {
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

        .field input {
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

        @media (max-width:900px) {
            .register-card {
                grid-template-columns: 1fr;
            }

            .side-brand {
                padding: 2rem;
                text-align: center;
            }
        }

        /* Estilos para toggle y mensajes de contraseña */
        .field .toggle-password {
            cursor: pointer;
            color: #6b7aa6;
            margin-left: .4rem;
            user-select: none;
        }

        .small.match-ok {
            color: #2f8e44;
        }

        .small.match-error {
            color: #d0464c;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="side-brand">
                <div class="logo">
                    <img src="{{ asset('favicon.ico') }}" alt="ServiceLink">
                    <h2>Service<span style="font-weight:700">Link</span></h2>
                </div>
                <p class="primary">Crea tu cuenta para acceder al panel de ServiceLink. Gestiona pedidos, menú y
                    colaboradores de forma rápida y segura.</p>
                <div style="display:flex;flex-direction:column;gap:.6rem;margin-top:1rem;">
                    <div style="display:flex;gap:.6rem;align-items:center;">
                        <span class="material-icons-sharp"
                            style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">security</span>
                        <span>Acceso seguro</span>
                    </div>
                    <div style="display:flex;gap:.6rem;align-items:center;">
                        <span class="material-icons-sharp"
                            style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">support</span>
                        <span>Soporte integrado</span>
                    </div>
                </div>
            </div>

            <div class="register-right">
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <h1 class="form-title">Crear Cuenta</h1>
                    <p class="small">Rellena los datos para crear una nueva cuenta</p>

                    <!-- RUT -->
                    <label class="field" for="rut" style="margin-top:1rem;">
                        <span class="material-icons-sharp">badge</span>
                        <input id="rut" inputmode="text" name="rut" maxlength="12"
                            placeholder="RUT (ej: 12.345.678-9)" value="{{ old('rut') }}" required autofocus>
                    </label>
                    @error('rut')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <div id="rutHelp" class="small" style="color:#d0464c;display:none;margin-top:.4rem;">RUT inválido
                    </div>

                    <!-- Nombre -->
                    <label class="field" for="nombre" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">person</span>
                        <input id="nombre" type="text" name="nombre" placeholder="Nombre"
                            value="{{ old('nombre') }}" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+">
                    </label>
                    @error('nombre')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Apellido -->
                    <label class="field" for="apellido" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">badge</span>
                        <input id="apellido" type="text" name="apellido" placeholder="Apellido"
                            value="{{ old('apellido') }}" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s]+">
                    </label>
                    @error('apellido')
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
                    <div id="emailHelp" class="small" aria-live="polite"
                        style="color:#d0464c;display:none;margin-top:.4rem;">Email inválido</div>

                    <!-- Contraseña -->
                    <label class="field" for="password" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">lock</span>
                        <input id="password" type="password" name="password" placeholder="Contraseña" required
                            autocomplete="new-password" maxlength="15">
                        <!-- toggle password -->
                        <span class="material-icons-sharp toggle-password" data-target="password"
                            title="Mostrar / ocultar contraseña">visibility</span>
                    </label>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <!-- Confirmación -->
                    <label class="field" for="password_confirmation" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">lock_clock</span>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Confirmar contraseña" required>
                        <!-- toggle password -->
                        <span class="material-icons-sharp toggle-password" data-target="password_confirmation"
                            title="Mostrar / ocultar contraseña">visibility</span>
                    </label>

                    <!-- Mensaje de coincidencia -->
                    <div id="password-match" class="small" style="margin-top:.4rem;"></div>

                    <!-- Fecha de nacimiento -->
                    <label class="field" for="fecha_nacimiento" style="margin-top:.6rem;">
                        <span class="material-icons-sharp">cake</span>
                        <input id="fecha_nacimiento" type="date" name="fecha_nacimiento"
                            value="{{ old('fecha_nacimiento') }}" required>
                    </label>
                    @error('fecha_nacimiento')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn-primary" style="margin-top:1rem;">Crear Cuenta</button>

                    <div style="margin-top:1rem;display:flex;justify-content:center;gap:.5rem;align-items:center;">
                        <span class="small">¿Ya tienes cuenta?</span>
                        <a href="/" class="link-muted" style="font-weight:600;">Inicia sesión</a>
                    </div>

                    <div style="margin-top:1.2rem;text-align:center;">
                        <a href="welcome" class="small link-muted">Volver al inicio</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('index.js') }}"></script>
    <script>
        // Autoformato para RUT + restringir caracteres (solo dígitos, puntos, guión y K/k)
        (function() {
            const rutInput = document.getElementById('rut');
            if (!rutInput) return;

            // Mientras el usuario escribe, eliminar cualquier carácter no permitido
            rutInput.addEventListener('input', function(e) {
                let v = e.target.value;
                // permitir dígitos, K/k, puntos y guión
                v = v.replace(/[^0-9kK.\-]/g, '');
                // mantener solo la última K/k si hay varias
                v = v.replace(/[kK](?=[\s\S]*[kK])/g, '');
                e.target.value = v;
            });

            rutInput.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/[^0-9kK]/g, '').toUpperCase();

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

        // Validar formato del RUT
        function isValidRUTFormat(rut) {
            return /^\d{1,2}\.\d{3}\.\d{3}-[0-9kK]$/.test(rut) || /^\d{1,8}-[0-9kK]$/.test(rut);
        }

        // Toggle ver/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = document.getElementById(this.dataset.target);
                if (!target) return;
                if (target.type === 'password') {
                    target.type = 'text';
                    this.textContent = 'visibility_off';
                } else {
                    target.type = 'password';
                    this.textContent = 'visibility';
                }
            });
        });

        // Comprobación en tiempo real si las contraseñas coinciden
        (function() {
            var pwd = document.getElementById('password');
            var pwdc = document.getElementById('password_confirmation');
            var matchDiv = document.getElementById('password-match');
            if (!pwd || !pwdc || !matchDiv) return;

            function checkMatch() {
                if (!pwd.value && !pwdc.value) {
                    matchDiv.textContent = '';
                    matchDiv.className = 'small';
                    return;
                }
                if (pwd.value === pwdc.value) {
                    matchDiv.textContent = 'Las contraseñas coinciden';
                    matchDiv.className = 'small match-ok';
                } else {
                    matchDiv.textContent = 'Las contraseñas no coinciden';
                    matchDiv.className = 'small match-error';
                }
            }

            pwd.addEventListener('input', checkMatch);
            pwdc.addEventListener('input', checkMatch);
        })();

        // Restringir nombre y apellido a letras y espacios
        (function() {
            const nameInput = document.getElementById('nombre');
            const lastInput = document.getElementById('apellido');
            const allowedRegex = /[^A-Za-zÀ-ÖØ-öø-ÿ\s]/g;

            function sanitizeName(e) {
                const el = e.target;
                if (!el) return;
                el.value = el.value.replace(allowedRegex, '');
            }

            if (nameInput) nameInput.addEventListener('input', sanitizeName);
            if (lastInput) lastInput.addEventListener('input', sanitizeName);
        })();

        // Validación en cliente del RUT (Dígito verificador)
        (function() {
            const rutInput = document.getElementById('rut');
            const rutHelp = document.getElementById('rutHelp');
            if (!rutInput) return;

            function computeDV(num) {
                let reversed = num.split('').reverse();
                let factor = 2;
                let sum = 0;
                for (let i = 0; i < reversed.length; i++) {
                    sum += parseInt(reversed[i], 10) * factor;
                    factor++;
                    if (factor > 7) factor = 2;
                }
                const resto = sum % 11;
                const calc = 11 - resto;
                if (calc === 11) return '0';
                if (calc === 10) return 'K';
                return String(calc);
            }

            function normalizeRut(value) {
                return value.replace(/\./g, '').replace(/\-/g, '').toUpperCase();
            }

            function isValidRUTWithDV(value) {
                const v = normalizeRut(value);
                if (v.length < 2) return false;
                const body = v.slice(0, -1);
                const dv = v.slice(-1);
                if (!/^\d+$/.test(body)) return false;
                const expected = computeDV(body);
                return expected === dv;
            }

            function updateRutValidity() {
                const val = rutInput.value.trim();
                // If empty, hide message
                if (!val) {
                    rutHelp.style.display = 'none';
                    rutInput.classList.remove('error');
                    return true;
                }

                if (!isValidRUTWithDV(val)) {
                    rutHelp.style.display = 'block';
                    rutHelp.textContent = 'RUT inválido';
                    rutInput.classList.add('error');
                    return false;
                }

                rutHelp.style.display = 'none';
                rutInput.classList.remove('error');
                return true;
            }

            rutInput.addEventListener('input', function() {
                // keep allowed chars only (digits, K/k, ., -)
                this.value = this.value.replace(/[^0-9kK.\-]/g, '');
                updateRutValidity();
            });

            rutInput.addEventListener('blur', function() {
                updateRutValidity();
            });

            // Prevent submit if RUT invalid
            const form = rutInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!updateRutValidity()) {
                        e.preventDefault();
                        rutInput.focus();
                    }
                });
            }
        })();

        // Validación en cliente del email: mostrar mensaje al terminar de escribir
        (function() {
            function initEmailValidation() {
                const emailInput = document.getElementById('email');
                const emailHelp = document.getElementById('emailHelp');
                if (!emailInput) return;

                const validEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

                function updateEmail() {
                    const v = emailInput.value.trim();
                    if (!v) {
                        if (emailHelp) emailHelp.style.display = 'none';
                        return true;
                    }
                    if (!validEmail(v)) {
                        if (emailHelp) {
                            emailHelp.style.display = 'block';
                            emailHelp.textContent = 'Correo inválido';
                        }
                        emailInput.classList.add('error');
                        return false;
                    }
                    if (emailHelp) emailHelp.style.display = 'none';
                    emailInput.classList.remove('error');
                    return true;
                }

                emailInput.addEventListener('blur', updateEmail);
                emailInput.addEventListener('change', updateEmail);
                emailInput.addEventListener('focusout', updateEmail);
                emailInput.addEventListener('input', function() {
                    // hide while typing, validate on blur
                    if (emailHelp) emailHelp.style.display = 'none';
                    emailInput.classList.remove('error');
                });

                // prevent submit if invalid
                const form = emailInput.closest('form');
                if (form) form.addEventListener('submit', function(e) {
                    if (!updateEmail()) {
                        e.preventDefault();
                        emailInput.focus();
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initEmailValidation);
            } else {
                initEmailValidation();
            }
        })();



        const hoy = new Date();
        hoy.setFullYear(hoy.getFullYear() - 18);

        const maxDate = hoy.toISOString().split('T')[0];
        document.getElementById('fecha_nacimiento').max = maxDate;
    </script>
</body>

</html>
