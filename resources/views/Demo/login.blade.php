<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('style.css') }}">
	<title>ServiceLink - Login</title>
	<style>
		/* Estilos específicos del login para mantener la estética sin tocar style.css */
		@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        body { background: linear-gradient(180deg, #f5f7fb 0%, #ffffff 100%); font-family: Poppins; }
		.login-container {
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 2rem;
		}
		.login-card {
			width: 100%;
			max-width: 980px;
			display: grid;
			grid-template-columns: 1fr 1fr;
			box-shadow: 0 10px 30px rgba(2,6,23,0.08);
			border-radius: 12px;
			overflow: hidden;
			background: #fff;
		}
		.login-left {
			background: linear-gradient(135deg, #5b9df9 0%, #3a73d9 100%);
			color: #fff;
			padding: 3rem;
			display:flex;
			flex-direction:column;
			justify-content:center;
			gap:1.2rem;
		}
		.login-left .logo { display:flex; align-items:center; gap:.8rem; }
		.login-left img { width:40px; height:40px; }
		.login-left h2 { margin:0; font-size:1.6rem; letter-spacing:0.4px; }
		.login-left p { margin:0; opacity:.95; max-width: 18rem; line-height:1.4; }
		.login-right {
			padding: 2.5rem;
			display:flex;
			flex-direction:column;
			justify-content:center;
			gap:1rem;
		}
        .primary {
            font-weight: 500;
            color : #fff;
        }
		.login-form h1 { margin:0 0 .5rem 0; }
		.field {
			display:flex;
			align-items:center;
			gap:.6rem;
			background:#f6f8fb;
			border-radius:8px;
			padding:.6rem .8rem;
			border:1px solid transparent;
			transition:all .15s ease;
		}
		.field:focus-within { box-shadow: 0 6px 18px rgba(58,115,217,0.08); border-color: rgba(58,115,217,0.18); background:#fff; }
		.field .material-icons-sharp { color: #6b7aa6; }
		.field input {
			border: none;
			background: transparent;
			outline: none;
			width:100%;
			font-size: .98rem;
			padding: .45rem 0;
		}
		.btn-primary {
			background: linear-gradient(90deg,#3a73d9,#5b9df9);
			color:#fff;
			border:none;
			padding:.75rem 1rem;
			border-radius:10px;
			cursor:pointer;
			font-weight:600;
			width:100%;
			box-shadow: 0 6px 18px rgba(58,115,217,0.12);
		}
		.actions { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-top:.6rem; }
		.link-muted { color:#6b7aa6; font-size:.92rem; text-decoration:none; }
		.small { font-size:.9rem; color:#6b7aa6; }
		@media (max-width:900px) {
			.login-card { grid-template-columns: 1fr; }
			.login-left { padding: 2rem; text-align:center; }
		}
	</style>
</head>
<body>
	<div class="login-container">
		<div class="login-card">
			<div class="login-left">
				<div class="logo">
					<img src="{{ asset('favicon.ico') }}" alt="ServiceLink">
					<h2>Service<span style="font-weight:700">Link</span></h2>
				</div>
				<p class="primary">Accede a tu panel de administración de ServiceLink. Controla ventas, pedidos y colaboradores desde una sola plataforma.</p>
				<!-- Opcional: pequeñas tarjetas de beneficios -->
				<div style="display:flex;flex-direction:column;gap:.6rem;margin-top:1rem;">
					<div style="display:flex;gap:.6rem;align-items:center;">
						<span class="material-icons-sharp" style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">dashboard</span>
						<span>Dashboard claro y fácil</span>
					</div>
					<div style="display:flex;gap:.6rem;align-items:center;">
						<span class="material-icons-sharp" style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">inventory</span>
						<span>Gestiona tu menú</span>
					</div>
				</div>
			</div>

			<div class="login-right">
				<form class="login-form" method="POST" action="{{ route('login.usuario.perform') }}">
					@csrf
					<h1>Iniciar Sesión</h1>
					<p class="small">Introduce tus credenciales para continuar</p>

					@if($errors->has('auth'))
						<div style="color:#b00020;margin-top:.6rem;">{{ $errors->first('auth') }}</div>
					@endif

					<label class="field" for="email" style="margin-top:1rem;">
						<span class="material-icons-sharp">mail_outline</span>
						<input id="email" type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>
					</label>

                    <label class="field" for="password" style="margin-top:.6rem; position:relative;">
                        <span class="material-icons-sharp">lock</span>
                        <input id="password" type="password" name="password" placeholder="Contraseña" required>
                        <button type="button" id="togglePassword" aria-label="Mostrar contraseña" style="background:transparent;border:none;cursor:pointer;color:#6b7aa6;display:flex;align-items:center;padding:0 .2rem;">
                            <span class="material-icons-sharp" id="toggleIcon">visibility</span>
                        </button>
                    </label>

                    <script>
                        (function(){
                            const pwd = document.getElementById('password');
                            const btn = document.getElementById('togglePassword');
                            const icon = document.getElementById('toggleIcon');
                            if(btn && pwd && icon){
                                btn.addEventListener('click', function(e){
                                    e.preventDefault();
                                    if(pwd.type === 'password'){
                                        pwd.type = 'text';
                                        icon.textContent = 'visibility_off';
                                        btn.setAttribute('aria-label','Ocultar contraseña');
                                    } else {
                                        pwd.type = 'password';
                                        icon.textContent = 'visibility';
                                        btn.setAttribute('aria-label','Mostrar contraseña');
                                    }
                                });
                            }
                        })();
                    </script>

					<div class="actions">
						<label style="display:flex;align-items:center;gap:.5rem;">
							<input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;">
							<span class="small">Recordarme</span>
						</label>
						<a class="link-muted" href="{{ route('forgot-password') }}">¿Olvidaste tu contraseña?</a>

					</div>

					<button type="submit" class="btn-primary" style="margin-top:1rem;">Entrar</button>

					<div style="margin-top:1rem;display:flex;justify-content:center;gap:.5rem;align-items:center;">
						<span class="small">¿No tienes cuenta?</span>
						<a href="{{ route('register') }}" class="link-muted" style="font-weight:600;">Regístrate</a>
					</div>

					<!-- Enlace rápido para volver al Dashboard si es necesario -->
					<div style="margin-top:1.2rem;text-align:center;">
						<a href="/" class="small link-muted">Volver al inicio</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="{{ asset('index.js') }}"></script>
</body>
</html>
