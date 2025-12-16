<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('style.css') }}">
	<title>ServiceLink - Login Empleado</title>
	<style>
		/* Reusar estilos del login principal */
		@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
		body { background: linear-gradient(180deg, #f5f7fb 0%, #ffffff 100%); font-family: Poppins; }
		.container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
		.card{width:100%;max-width:640px;background:#fff;padding:2rem;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,0.06)}
		.field{display:flex;align-items:center;gap:.6rem;background:#f6f8fb;border-radius:8px;padding:.6rem .8rem}
		.btn-primary{background:linear-gradient(90deg,#3a73d9,#5b9df9);color:#fff;border:none;padding:.75rem 1rem;border-radius:10px;cursor:pointer;font-weight:600;width:100%}
	</style>
</head>
<body>
	<div class="container">
		<div class="card">
			<h1>Ingreso Empleado</h1>
			<p class="small">Usa tu correo y contraseña asignada</p>

			@if($errors->has('auth'))
				<div style="color:#b00020;margin-top:.6rem;">{{ $errors->first('auth') }}</div>
			@endif

			<form method="POST" action="{{ route('login.empleado.perform') }}">
				@csrf
				<label class="field" for="email" style="margin-top:1rem;">
					<span class="material-icons-sharp">mail_outline</span>
					<input id="email" type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>
				</label>

				<label class="field" for="password" style="margin-top:.6rem;">
					<span class="material-icons-sharp">lock</span>
					<input id="password" type="password" name="password" placeholder="Contraseña" required>
				</label>

				<button type="submit" class="btn-primary" style="margin-top:1rem;">Entrar</button>
			</form>

			<div style="margin-top:1rem;display:flex;justify-content:space-between;align-items:center;">
				<a href="{{ route('login') }}" class="small" style="color:#6b7aa6;">Volver</a>
				<a href="{{ route('welcome') }}" class="small" style="color:#6b7aa6;">Volver al inicio</a>
			</div>
		</div>
	</div>
</body>
</html>