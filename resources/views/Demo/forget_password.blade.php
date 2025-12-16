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
				<p class="primary">¿Olvidaste tu contraseña? </p>
                <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
			</div>

			<div class="login-right">
				<form class="login-form" method="POST" action="{{ route('forgot-password.send') }}">
					@csrf
					<h1>Restablecer contraseña</h1>
					<p class="small">Introduce el correo electrónico asociado a tu cuenta. Te enviaremos un enlace para establecer una nueva contraseña.</p>

					@if(session('status'))
						<div style="color:green;margin-top:.6rem;">{{ session('status') }}</div>
					@endif

					@if($errors->has('email'))
						<div style="color:#b00020;margin-top:.6rem;">{{ $errors->first('email') }}</div>
					@endif

					<label class="field" for="email" style="margin-top:1rem;">
						<span class="material-icons-sharp">mail_outline</span>
						<input id="email" type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>
					</label>

					<!-- reCAPTCHA v3 token (se rellena antes de enviar) -->
					<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="">

					<div style="margin-top:1rem;">
						<button type="submit" id="sendBtn" class="btn-primary" disabled>Enviar enlace</button>
					</div>

					<!-- Enlace rápido para volver al Dashboard si es necesario -->
					<div style="margin-top:1.2rem;text-align:center;">
						<a href="{{ route('login') }}" class="small link-muted">Volver al inicio</a>
					</div>
				</form>

				<!-- Google reCAPTCHA v3 -->
				<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
				<script>
					(function(){
						const email = document.getElementById('email');
						const sendBtn = document.getElementById('sendBtn');
						const tokenInput = document.getElementById('g-recaptcha-response');

						function validateEmail(value) {
							return /^(?:[a-zA-Z0-9_'^&\/+%~-]+(?:\.[a-zA-Z0-9_'^&\/+%~-]+)*|"(?:\\[\s\S]|[^\\"])*")@(?:(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})$/.test(value);
						}

						function updateButtonState() {
							const ok = email && sendBtn;
							if (!ok) return;
							const validEmail = validateEmail(email.value.trim());
							sendBtn.disabled = !validEmail;
						}

						if (email) email.addEventListener('input', updateButtonState);
						// Inicializar estado
						updateButtonState();

						// Interceptar envío para solicitar token reCAPTCHA y luego enviar
						const form = document.querySelector('.login-form');
						if (form) {
							form.addEventListener('submit', function(e){
								e.preventDefault();
								sendBtn.disabled = true;
								grecaptcha.ready(function(){
									grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'forgot_password'}).then(function(token){
										if (tokenInput) tokenInput.value = token;
										form.submit();
									});
								});
							});
						}
					})();
				</script>
			</div>
		</div>
	</div>

	<script src="{{ asset('index.js') }}"></script>
</body>
</html>
