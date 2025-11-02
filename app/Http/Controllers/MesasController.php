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
       /*  $mesas = Mesas::with('restaurante')->get();
        return view('mesas.index', compact('mesas')); */
        return view('mesas.index');
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
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'restaurante_id' => 'required|exists:restaurantes,id',
        ]);

        $table = new Mesas();
        $table->nombre = $data['nombre'];
        $table->estado = $data['estado'];
        $table->restaurante_id = $data['restaurante_id'];
        $table->save();

        return redirect()->route('mesas.index')->with('success', 'Mesa creada exitosamente.');
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
    public function update(Request $request, Mesas $mesas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mesas $mesas)
    {
        //
    }
}
