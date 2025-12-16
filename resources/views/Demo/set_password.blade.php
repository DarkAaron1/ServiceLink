<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña</title>
    <style>
        :root {
            --bg1: #f6f8fa;
            --card: #ffffff;
            --accent: #6C9BCF;
            --danger: #dc2626;
        }

        /* Usar colores del proyecto: --accent igual que --color-primary en public/style.css (#6C9BCF) */
        body {
            margin: 0;
            font-family: Segoe UI, Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #f3f7fb, #ffffff);
            color: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 480px;
            padding: 24px;
        }

        .card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(16, 24, 40, 0.08);
            padding: 28px;
        }

        h2 {
            margin: 0 0 8px 0;
            font-size: 20px;
        }

        p.lead {
            margin: 0 0 18px 0;
            color: #4b5563;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .input-row {
            position: relative;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 40px 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            padding: 6px;
            cursor: pointer;
        }

        /* Forzar color de los iconos a color de marca */
        .toggle-btn svg {
            stroke: var(--accent) !important;
        }

        .help {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }

        .help.error {
            color: var(--danger);
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        button.primary {
            background: var(--accent);
            color: white;
            border: 0;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(108, 155, 207, 0.18);
        }

        button.secondary {
            background: transparent;
            border: 1px solid rgba(108, 155, 207, 0.14);
            color: var(--accent);
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        button[disabled] {
            opacity: 0.56;
            cursor: not-allowed;
        }

        .meter {
            height: 8px;
            background: rgba(108, 155, 207, 0.12);
            border-radius: 99px;
            overflow: hidden;
            margin-top: 8px;
        }

        .meter>i {
            display: block;
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent), #2b6cb0);
        }

        .note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2>Cambiar contraseña</h2>
            <p class="lead">Introduce tu nueva contraseña y repítela. Debe tener al menos 8 caracteres.</p>

            @php
                $tokenValue = $token ?? request()->route('token') ?? request()->query('token');
            @endphp
            <form method="POST" action="{{ route('set-password.post', ['token' => $tokenValue]) }}" id="changePasswordForm" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ old('token', $tokenValue) }}">
                <input type="hidden" name="email" value="{{ old('email', $email ?? request()->query('email')) }}">

                <div class="field">
                    <label for="password">Contraseña nueva</label>
                    <div class="input-row">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            minlength="8" />
                        <button type="button" class="toggle-btn" aria-label="Mostrar contraseña"
                            data-target="password">
                            <!-- eye icon (closed by default) -->
                            <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div class="help" id="passwordHelp">Mínimo 8 caracteres.</div>
                    <div class="meter" aria-hidden="true"><i id="strengthBar"></i></div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Repetir contraseña</label>
                    <div class="input-row">
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            autocomplete="new-password" required />
                        <button type="button" class="toggle-btn" aria-label="Mostrar repetir contraseña"
                            data-target="password_confirmation">
                            <svg id="eye-password_confirmation" xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div class="help" id="confirmHelp">Introduce la misma contraseña.</div>
                </div>

                <div class="actions">
                    <button type="submit" id="saveBtn" class="primary" disabled>Guardar</button>
                    <button type="button" class="secondary" onclick="history.back(); return false;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const saveBtn = document.getElementById('saveBtn');
            const passHelp = document.getElementById('passwordHelp');
            const confirmHelp = document.getElementById('confirmHelp');
            const strengthBar = document.getElementById('strengthBar');

            function updateStrength(value) {
                // simple strength meter based on length
                const len = value.length;
                let pct = Math.min(100, Math.floor((len / 12) * 100));
                if (len >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value)) pct = 100;
                else if (len >= 10) pct = 80;
                strengthBar.style.width = pct + '%';
            }

            function validate() {
                const p = password.value || '';
                const c = confirm.value || '';
                let ok = true;

                if (p.length < 8) {
                    passHelp.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                    passHelp.classList.add('error');
                    ok = false;
                } else {
                    passHelp.textContent = 'Mínimo 8 caracteres.';
                    passHelp.classList.remove('error');
                }

                if (c.length === 0) {
                    confirmHelp.textContent = 'Introduce la misma contraseña.';
                    confirmHelp.classList.remove('error');
                    ok = false;
                } else if (p !== c) {
                    confirmHelp.textContent = 'Las contraseñas no coinciden.';
                    confirmHelp.classList.add('error');
                    ok = false;
                } else {
                    confirmHelp.textContent = 'Las contraseñas coinciden.';
                    confirmHelp.classList.remove('error');
                }

                updateStrength(p);
                saveBtn.disabled = !ok;
            }

            password.addEventListener('input', validate);
            confirm.addEventListener('input', validate);

            // Toggle show/hide
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    if (input.type === 'password') {
                        input.type = 'text';
                        btn.setAttribute('aria-label', 'Ocultar contraseña');
                        // change icon to indicate open eye (simple swap: invert fill)
                        btn.querySelector('svg').style.filter = 'brightness(0.6)';
                    } else {
                        input.type = 'password';
                        btn.setAttribute('aria-label', 'Mostrar contraseña');
                        btn.querySelector('svg').style.filter = '';
                    }
                });
            });

            // Prevent submit if invalid (extra guard)
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                validate();
                if (saveBtn.disabled) {
                    e.preventDefault();
                    password.focus();
                }
            });

            // initial check in case of pre-filled fields
            validate();
        })();
    </script>
    <script>
        // Ensure token is set from URL (path, query or hash) in case server-render didn't include it
        (function(){
            try {
                const tokenInput = document.querySelector('input[name="token"]');
                if (!tokenInput) return;
                if (tokenInput.value) return; // already set

                // 1) check path: /set-password/TOKEN
                const pathMatch = window.location.pathname.match(/\/set-password\/(.+)$/);
                if (pathMatch && pathMatch[1]) {
                    tokenInput.value = decodeURIComponent(pathMatch[1]);
                }

                // 2) check query string
                if (!tokenInput.value) {
                    const params = new URLSearchParams(window.location.search);
                    if (params.has('token')) tokenInput.value = params.get('token');
                }

                // 3) check hash fragment (#token=..., #email=...)
                if (!tokenInput.value && window.location.hash) {
                    const hash = window.location.hash.replace(/^#/, '');
                    const hp = new URLSearchParams(hash);
                    if (hp.has('token')) tokenInput.value = hp.get('token');
                    if (hp.has('email')) {
                        const emailInput = document.querySelector('input[name="email"]');
                        if (emailInput && !emailInput.value) emailInput.value = hp.get('email');
                    }
                }

                // also ensure form action includes token and email (in case JS submitted without them)
                const form = document.getElementById('changePasswordForm');
                const emailInput = document.querySelector('input[name="email"]');
                if (form && (tokenInput.value || (emailInput && emailInput.value))) {
                    const actionUrl = new URL(form.action, window.location.origin);
                    if (tokenInput.value) actionUrl.searchParams.set('token', tokenInput.value);
                    if (emailInput && emailInput.value) actionUrl.searchParams.set('email', emailInput.value);
                    form.action = actionUrl.pathname + (actionUrl.search ? actionUrl.search : '');
                }
            } catch (e) {
                console.error('token autofill error', e);
            }
        })();
    </script>
</body>

</html>
