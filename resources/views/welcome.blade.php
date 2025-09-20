<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <style>
        body{ margin:0; height:100vh; display:flex; align-items:center; justify-content:center; font-family:Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg,#74ebd5,#ACB6E5); color:#063;}
        .card{ background:rgba(255,255,255,0.12); padding:2rem; border-radius:12px; border:1px solid rgba(255,255,255,0.14); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Bienvenido</h1>
        <p>Inicio exitoso. Puedes ir a <a href="{{ route('admin.index') }}">Administración de usuarios</a>.</p>
    </div>
</body>
</html>
