@component('mail::message')
{{-- OJO: Usamos corchetes ['nombre'] porque $datos es un array --}}
# Hola {{ $datos['nombre'] }} {{ $datos['apellido'] }},

Se ha enviado un correo para que puedas cambiar tu contraseña.

Tal como solicitaste,

Por favor, ingresa al siguiente enlace para acceder y te recomendamos cambiarla nuevamente en tu perfil.

@php
	$link = $datos['link'] ?? (isset($datos['email']) ? route('set-password', ['email' => $datos['email']]) : route('set-password'));
@endphp

@component('mail::button', ['url' => $link])
Establecer Contraseña
@endcomponent

Con cariño,
{{ config('app.name') }}
@endcomponent