@component('mail::message')
# ¡Hola {{ $datos['nombre'] }} {{ $datos['apellido'] }}! 👋

Te damos la bienvenida al sistema de **{{ config('app.name') }}**.

Tu cuenta ha sido creada con éxito. Para tu primer acceso, utiliza las siguientes credenciales:


Ingrese al siguiente link para establecer tu contraseña:

- **Correo Electrónico:** {{ $datos['email'] }}

@component('mail::button', ['url' => route('set-password')])
Establecer Contraseña
@endcomponent

Si tienes algún problema para acceder, contacta con el administrador.

Saludos cordiales,
{{ config('app.name') }}
@endcomponent