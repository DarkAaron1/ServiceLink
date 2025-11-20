@props(['action', 'method' => 'POST', 'usuario' => null, 'inModal' => false])

<form action="{{ $action }}" method="POST" style="margin-top: 20px;">
    @csrf
    @if(strtoupper($method) !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div style="background:#fff3cd; border:1px solid #ffeeba; padding:10px; border-radius:4px; margin-bottom:12px;">
            <strong>Hay errores con los datos enviados:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label for="rut" style="display: block; margin-bottom: 5px; font-weight: bold;">Rut:</label>
            <input type="text" id="rut" name="rut" value="{{ old('rut', optional($usuario)->rut) }}" {{ $usuario ? 'disabled' : 'required' }} style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="nombre" style="display: block; margin-bottom: 5px; font-weight: bold;">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', optional($usuario)->nombre) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="apellido" style="display: block; margin-bottom: 5px; font-weight: bold;">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', optional($usuario)->apellido) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Correo:</label>
            <input type="email" id="email" name="email" value="{{ old('email', optional($usuario)->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        @if(!$usuario)
        <div>
            <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Contraseña:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="password_confirmation" style="display: block; margin-bottom: 5px; font-weight: bold;">Confirmar Contraseña:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        @endif

        <div>
            <label for="fecha_nacimiento" style="display: block; margin-bottom: 5px; font-weight: bold;">Fecha Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($usuario)->fecha_nacimiento ? optional($usuario)->fecha_nacimiento->format('Y-m-d') : null) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="fecha_creacion" style="display: block; margin-bottom: 5px; font-weight: bold;">Fecha Creación:</label>
            <input type="date" id="fecha_creacion" name="fecha_creacion" value="{{ old('fecha_creacion', optional($usuario)->created_at ? optional($usuario)->created_at->format('Y-m-d') : null) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div>
            <label for="rol_id" style="display: block; margin-bottom: 5px; font-weight: bold;">Rol:</label>
            <select id="rol_id" name="rol_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Seleccionar Rol --</option>
                <option value="1" {{ old('rol_id', optional($usuario)->rol_id) == 1 ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ old('rol_id', optional($usuario)->rol_id) == 2 ? 'selected' : '' }}>Gerente</option>
                <option value="3" {{ old('rol_id', optional($usuario)->rol_id) == 3 ? 'selected' : '' }}>Mesero</option>
                <option value="4" {{ old('rol_id', optional($usuario)->rol_id) == 4 ? 'selected' : '' }}>Cocinero</option>
            </select>
        </div>

        <div>
            <label for="estado" style="display: block; margin-bottom: 5px; font-weight: bold;">Estado:</label>
            <select id="estado" name="estado" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="1" {{ old('estado', optional($usuario)->estado) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('estado', optional($usuario)->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 10px;">
        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 30px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">{{ $usuario ? 'Actualizar Usuario' : 'Crear Usuario' }}</button>
        @if($inModal)
            <button type="button" id="cancel-new-user" style="background-color: #6c757d; color: white; padding: 10px 30px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Cancelar</button>
        @else
            <a href="{{ route('usuarios.index') }}" id="cancel-new-user" style="background-color: #6c757d; color: white; padding: 10px 30px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Cancelar</a>
        @endif
    </div>
</form>
