<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <title>Seleccionar tipo de acceso</title>
    <style>
        body{font-family:Poppins, sans-serif;background:linear-gradient(180deg,#f5f7fb 0,#fff 100%);display:flex;align-items:center;justify-content:center;min-height:100vh}
        .box{background:#fff;padding:2rem;border-radius:10px;box-shadow:0 10px 30px rgba(2,6,23,0.06);width:100%;max-width:720px;text-align:center}
        .actions{display:flex;gap:1rem;justify-content:center;margin-top:1.2rem}
        .btn{padding:.9rem 1.1rem;border-radius:8px;border:none;cursor:pointer;font-weight:600}
        .btn-usuario{background:linear-gradient(90deg,#3a73d9,#5b9df9);color:#fff}
        .btn-empleado{background:#f3f4f6;border:1px solid #dfe3ee;color:#111}
    </style>
</head>
<body>
    <div class="box">
        <h1>¿Cómo quieres acceder?</h1>
        <p>Selecciona si quieres iniciar sesión como <strong>Usuario</strong> o <strong>Empleado</strong></p>
        <div class="actions">
            <a href="{{ route('login.usuario') }}" class="btn btn-usuario">Soy Usuario</a>
            <a href="{{ route('login.empleado') }}" class="btn btn-empleado">Soy Empleado</a>
        </div>
        <div style="margin-top:1rem;color:#6b7aa6;font-size:.95rem;">
            <a href="{{ route('welcome') }}">Volver al inicio</a>
        </div>
    </div>
</body>
</html>