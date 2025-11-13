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

		body { background: linear-gradient(180deg, #f5f7fb 0%, #ffffff 100%); font-family: Poppins; }
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
			box-shadow: 0 10px 30px rgba(2,6,23,0.08);
			border-radius: 12px;
			overflow: hidden;
			background: #fff;
		}
		.side-brand {
			background: linear-gradient(135deg, #5b9df9 0%, #3a73d9 100%);
			color: #fff;
			padding: 3rem;
			display:flex;
			flex-direction:column;
			justify-content:center;
			gap:1.2rem;
		}
		.side-brand .logo { display:flex; align-items:center; gap:.8rem; }
		.side-brand img { width:40px; height:40px; }
		.side-brand h2 { margin:0; font-size:1.6rem; letter-spacing:0.4px; }
		.side-brand p { margin:0; opacity:.95; max-width: 18rem; line-height:1.4; }
		.register-right {
			padding: 2.5rem;
			display:flex;
			flex-direction:column;
			justify-content:center;
			gap:1rem;
		}
		.form-title { margin:0 0 .5rem 0; }
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
        .primary{
            font-weight: 500;
            color: #fff;
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
		.link-muted { color:#6b7aa6; font-size:.92rem; text-decoration:none; }
		.small { font-size:.9rem; color:#6b7aa6; }
		.error { color:#d0464c; font-size:.9rem; margin-top:.4rem; }
		@media (max-width:900px) {
			.register-card { grid-template-columns: 1fr; }
			.side-brand { padding: 2rem; text-align:center; }
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
				<p class="primary">Crea tu cuenta para acceder al panel de ServiceLink. Gestiona pedidos, menú y colaboradores de forma rápida y segura.</p>
				<div style="display:flex;flex-direction:column;gap:.6rem;margin-top:1rem;">
					<div style="display:flex;gap:.6rem;align-items:center;">
						<span class="material-icons-sharp" style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">security</span>
						<span>Acceso seguro</span>
					</div>
					<div style="display:flex;gap:.6rem;align-items:center;">
						<span class="material-icons-sharp" style="background:rgba(255,255,255,0.12);padding:.4rem;border-radius:.5rem;">support</span>
						<span>Soporte integrado</span>
					</div>
				</div>
			</div>

			<div class="register-right">
				<form method="POST" action="">
					@csrf
					<h1 class="form-title">Crear Cuenta</h1>
					<p class="small">Rellena los datos para crear una nueva cuenta</p>

					<label class="field" for="name" style="margin-top:1rem;">
						<span class="material-icons-sharp">person</span>
						<input id="name" type="text" name="name" placeholder="Nombre completo" value="" required autofocus>
					</label>
					@error('name') <div class="error">{{ $message }}</div> @enderror

					<label class="field" for="email" style="margin-top:.6rem;">
						<span class="material-icons-sharp">mail_outline</span>
						<input id="email" type="email" name="email" placeholder="Correo electrónico" value="" required>
					</label>
					@error('email') <div class="error">{{ $message }}</div> @enderror

					<label class="field" for="password" style="margin-top:.6rem;">
						<span class="material-icons-sharp">lock</span>
						<input id="password" type="password" name="password" placeholder="Contraseña" required autocomplete="new-password">
					</label>
					@error('password') <div class="error">{{ $message }}</div> @enderror

					<label class="field" for="password_confirmation" style="margin-top:.6rem;">
						<span class="material-icons-sharp">lock_clock</span>
						<input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
					</label>

					<button type="submit" class="btn-primary" style="margin-top:1rem;">Crear Cuenta</button>

					<div style="margin-top:1rem;display:flex;justify-content:center;gap:.5rem;align-items:center;">
						<span class="small">¿Ya tienes cuenta?</span>
						<a href="" class="link-muted" style="font-weight:600;">Inicia sesión</a>
					</div>

					<div style="margin-top:1.2rem;text-align:center;">
						<a href="" class="small link-muted">Volver al inicio</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="{{ asset('index.js') }}"></script>
</body>
</html>
