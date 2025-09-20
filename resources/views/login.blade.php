<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Liquid Glass</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
        }
        .glass-container {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .glass-container h2 {
            margin-bottom: 1.5rem;
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .form-group {
            width: 100%;
            margin-bottom: 1.2rem;
        }
        .form-group label {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.3rem;
            display: block;
        }
        .form-group input {
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            border: none;
            outline: none;
            background: rgba(255,255,255,0.25);
            color: #333;
            font-size: 1rem;
            margin-top: 0.2rem;
            box-shadow: 0 2px 8px rgba(31,38,135,0.10);
            transition: background 0.2s;
        }
        .form-group input:focus {
            background: rgba(255,255,255,0.4);
        }
        .login-btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #74ebd5 0%, #ACB6E5 100%);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.5rem;
            box-shadow: 0 4px 16px rgba(31,38,135,0.10);
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #ACB6E5 0%, #74ebd5 100%);
        }
        @media (max-width: 480px) {
            .glass-container {
                padding: 2rem 1rem;
                max-width: 95vw;
            }
        }
    </style>
</head>
<body>
    <div class="glass-container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button class="login-btn" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>