<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Empleados;
use App\Models\Mesas;
use Illuminate\Http\Request;

class ComandaController extends Controller
{
    /**
     * Mostrar listado de comandas.
     */
    public function index()
    {
        $comandas = Comanda::with(['empleado', 'mesa'])->orderBy('created_at', 'desc')->get();
        $empleados = Empleados::all();
        $mesas = Mesas::all();
        return view('Comandas.index', compact('comandas', 'empleados', 'mesas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empleados = Empleados::all();
        $mesas = Mesas::all();
        return view('Comandas.create', compact('empleados', 'mesas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut_empleado' => 'required|exists:empleados,rut',
            'mesa_id' => 'required|exists:mesas,id',
            'estado' => 'required|in:en_preparacion,listo,entregado',
            'fecha_apertura' => 'required|date',
            'fecha_cierre' => 'nullable|date',
        ]);

        Comanda::create($validated);

        return redirect()->route('comandas.index')->with('success', 'Comanda creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comanda $comanda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comanda $comanda)
    {
        $empleados = Empleados::all();
        $mesas = Mesas::all();
        return view('Comandas.edit', compact('comanda', 'empleados', 'mesas'));
    }

    /**
     * Get the edit form for AJAX request
     */
    public function getEditForm(Comanda $comanda)
    {
        $empleados = Empleados::all();
        $mesas = Mesas::all();
        return view('Comandas._form', [
            'action' => route('comandas.update', $comanda),
            'method' => 'PATCH',
            'comanda' => $comanda,
            'inModal' => true,
            'empleados' => $empleados,
            'mesas' => $mesas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comanda $comanda)
    {
        $validated = $request->validate([
            'rut_empleado' => 'required|exists:empleados,rut',
            'mesa_id' => 'required|exists:mesas,id',
            'estado' => 'required|in:en_preparacion,listo,entregado',
            'fecha_apertura' => 'required|date',
            'fecha_cierre' => 'nullable|date',
        ]);

        $comanda->update($validated);

        return redirect()->route('comandas.index')->with('success', 'Comanda actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comanda $comanda)
    {
        $comanda->delete();
        return redirect()->route('comandas.index')->with('success', 'Comanda eliminada exitosamente');
    }
}
