@component('mail::message')
{{-- OJO: Usamos corchetes ['nombre'] porque $datos es un array --}}
# Hola {{ $datos['nombre'] }} {{ $datos['apellido'] }},

Se ha restablecido tu contraseña con éxito en el sistema.

Tal como solicitaste, tu contraseña temporal es tu **RUT (sin puntos ni dígito verificador)**.

Por favor, ingresa al siguiente enlace para acceder y te recomendamos cambiarla nuevamente en tu perfil.

@component('mail::button', ['url' => route('login')]) {{-- Usa route() en lugar de http://127.0... --}}
Ir al Login
@endcomponent

Con cariño,
{{ config('app.name') }}
@endcomponent