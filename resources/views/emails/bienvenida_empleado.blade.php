@component('mail::message')
# ¡Hola {{ $datos['nombre'] }} {{ $datos['apellido'] }}! 👋

Te damos la bienvenida al sistema de **{{ config('app.name') }}**.

Tu cuenta ha sido creada con éxito. Para tu primer acceso, utiliza las siguientes credenciales:

* **Usuario (RUT):** Tu RUT completo.
* **Contraseña Temporal:** Tu RUT (sin puntos ni dígito verificador).

Es obligatorio que, una vez dentro del sistema, procedas a **cambiar tu contraseña de inmediato** por seguridad.

@component('mail::button', ['url' => route('login.empleado')])
Ir al Sistema (Login Empleado)
@endcomponent

Si tienes algún problema para acceder, contacta con el administrador.

Saludos cordiales,
{{ config('app.name') }}
@endcomponent