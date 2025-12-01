<?php

namespace App\Http\Controllers;

use App\Models\Items_Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Items_Menu;
use App\Models\Restaurante;

class ItemsCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mostrar categorías del restaurante del admin si existe contexto
        $rutSesion = request()->session()->get('usuario_rut');
        $restauranteId = null;
        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        if ($restauranteId) {
            $categorias = Items_Categoria::where('restaurante_id', $restauranteId)->get();
        } else {
            $categorias = Items_Categoria::all();
        }
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Determinar restaurante en contexto
        $restauranteId = null;
        $rutSesion = request()->session()->get('usuario_rut');
        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ];

        if ($restauranteId) {
            // unique per restaurante
            $rules['nombre'] = 'required|string|max:255|unique:items__categorias,nombre,NULL,id,restaurante_id,' . $restauranteId;
        }

        $validated = $request->validate($rules);

        $categoria = new Items_Categoria();
        $categoria->nombre = $validated['nombre'];
        $categoria->descripcion = $validated['descripcion'] ?? null;
        $categoria->restaurante_id = $restauranteId;
        $categoria->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Categoría creada', 'data' => $categoria], 201);
        }

        return redirect()->route('categorias.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Items_Categoria $items_Categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Items_Categoria $items_Categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items_Categoria $items_Categoria)
    {
        // Obtener restaurante en contexto para validar unicidad por restaurante
        $restauranteId = null;
        $rutSesion = request()->session()->get('usuario_rut');
        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ];

        if ($restauranteId) {
            $rules['nombre'] = 'required|string|max:255|unique:items__categorias,nombre,' . $items_Categoria->id . ',id,restaurante_id,' . $restauranteId;
        }

        $validated = $request->validate($rules);

        try {
            $items_Categoria->nombre = $validated['nombre'];
            $items_Categoria->descripcion = $validated['descripcion'] ?? null;
            $items_Categoria->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Categoría actualizada', 'data' => $items_Categoria]);
            }

            return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar la categoría', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Items_Categoria $items_Categoria)
    {
        // Contar items solamente con la misma association de restaurante (si aplica)
        $query = Items_Menu::where('categoria_id', $items_Categoria->id);
        if (! is_null($items_Categoria->restaurante_id)) {
            $query->where('restaurante_id', $items_Categoria->restaurante_id);
        }
        $count = $query->count();

        // Si la petición viene por AJAX/Fetch, responder en JSON para que el frontend muestre el modal
        if (request()->ajax() || request()->wantsJson()) {
            // Si no hay confirmación y la categoría está en uso, notificar al cliente
            if (!$this->hasConfirm() && $count > 0) {
                return response()->json([
                    'in_use' => true,
                    'count' => $count,
                    'message' => "La categoría está siendo usada por {$count} item(s)."
                ], 200);
            }

            // Si llegó confirmación o no está en uso, eliminar
            try {
                $items_Categoria->delete();
                return response()->json(['success' => true, 'message' => 'Categoría eliminada correctamente.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar la categoría', 'error' => $e->getMessage()], 500);
            }
        }

        // Petición normal (no-AJAX)
        if (!$this->hasConfirm() && $count > 0) {
            return redirect()->route('categorias.index')->with('warning', "La categoría está siendo usada por {$count} item(s). Confirme antes de eliminar.");
        }

        try {
            $items_Categoria->delete();
            return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('categorias.index')->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Helper para determinar si la petición incluye confirmación explícita.
     */
    protected function hasConfirm()
    {
        $req = request();
        // Puede enviarse como parametro 'confirm' o 'force'
        return $req->boolean('confirm') || $req->input('confirm') === '1' || $req->boolean('force') || $req->input('force') === '1';
    }
}
