<?php

namespace App\Http\Controllers;

use App\Models\Mesas;
use Illuminate\Http\Request;

class MesasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mesas = Mesas::with('restaurante')->get();
        return view('mesas.index', compact('mesas'));
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada',
        ]);

        try {
            $mesa = new Mesas();
            $mesa->nombre = $data['nombre'];
            $mesa->estado = $data['estado'];
            // Temporalmente asignamos un restaurante_id fijo (deberías ajustar esto según tu lógica de negocio)
            $mesa->restaurante_id = 1;
            $mesa->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesa creada exitosamente',
                    'mesa' => $mesa
                ]);
            }

            return redirect()->route('mesas.index')->with('success', 'Mesa creada exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la mesa',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al crear la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mesas $mesas)
    {
        return response()->json(Mesas::findOrFail($mesas->id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mesas $mesas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada',
        ]);

        try {
            $mesa = Mesas::findOrFail($id);
            $mesa->nombre = $data['nombre'];
            $mesa->estado = $data['estado'];
            $mesa->save();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Mesa actualizada correctamente.', 'mesa' => $mesa]);
            }

            return redirect()->route('mesas.index')->with('success', 'Mesa actualizada correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la mesa',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al actualizar la mesa: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
        // Buscar la mesa por su ID
        $mesa = Mesas::find($id);

        // Verificar si existe
        if (!$mesa) {
            return redirect()->route('mesas.index')->with('error', 'La mesa no fue encontrada.');
        }

        // Eliminar la mesa
        $mesa->delete();

        // Redirigir con mensaje de éxito
        return redirect()->route('mesas.index')->with('success', 'Mesa eliminada correctamente.');
    }
}
