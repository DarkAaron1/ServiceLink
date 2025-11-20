@props(['action', 'method' => 'POST', 'comanda' => null, 'inModal' => false])

<form action="{{ $action }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <!-- Mostrar errores -->
    @if ($errors->any())
        <div style="background:#f8d7da; border:1px solid #f5c6cb; padding:12px; border-radius:4px; color:#721c24;">
            <strong>Errores en el formulario:</strong>
            <ul style="margin:8px 0 0 20px; padding:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- RUT Empleado -->
    <div>
        <label for="rut_empleado" style="display: block; font-weight: bold; margin-bottom: 5px;">RUT Empleado *</label>
        <input type="text" name="rut_empleado" id="rut_empleado" value="{{ old('rut_empleado') ?? $comanda?->rut_empleado ?? '' }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" placeholder="Ingresa el RUT del empleado">
        @error('rut_empleado')
            <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Mesa -->
    <div>
        <label for="mesa_id" style="display: block; font-weight: bold; margin-bottom: 5px;">ID Mesa *</label>
        <input type="number" name="mesa_id" id="mesa_id" value="{{ old('mesa_id') ?? $comanda?->mesa_id ?? '' }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" placeholder="Ingresa el ID de la mesa">
        @error('mesa_id')
            <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Estado -->
    <div>
        <label for="estado" style="display: block; font-weight: bold; margin-bottom: 5px;">Estado *</label>
        <select name="estado" id="estado" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            <option value="">Selecciona un estado</option>
            <option value="en_preparacion" @selected(old('estado') == 'en_preparacion' || $comanda?->estado == 'en_preparacion')>En preparación</option>
            <option value="listo" @selected(old('estado') == 'listo' || $comanda?->estado == 'listo')>Listo</option>
            <option value="entregado" @selected(old('estado') == 'entregado' || $comanda?->estado == 'entregado')>Entregado</option>
        </select>
        @error('estado')
            <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Fecha Apertura -->
    <div>
        <label for="fecha_apertura" style="display: block; font-weight: bold; margin-bottom: 5px;">Fecha Apertura *</label>
        <input type="datetime-local" name="fecha_apertura" id="fecha_apertura" value="{{ old('fecha_apertura') ?? ($comanda?->fecha_apertura?->format('Y-m-d\TH:i') ?? '') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        @error('fecha_apertura')
            <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Fecha Cierre (opcional) -->
    <div>
        <label for="fecha_cierre" style="display: block; font-weight: bold; margin-bottom: 5px;">Fecha Cierre</label>
        <input type="datetime-local" name="fecha_cierre" id="fecha_cierre" value="{{ old('fecha_cierre') ?? ($comanda?->fecha_cierre?->format('Y-m-d\TH:i') ?? '') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        @error('fecha_cierre')
            <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Botones -->
    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        @if($inModal)
            <button type="button" id="cancel-new-comanda" style="background-color: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Cancelar</button>
        @else
            <a href="{{ route('comandas.index') }}" style="background-color: #6c757d; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Cancelar</a>
        @endif
        <button type="submit" style="background-color: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">{{ $comanda ? 'Actualizar' : 'Crear' }}</button>
    </div>
</form>
