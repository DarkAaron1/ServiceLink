<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( request $request)
    {
        // Si no hay sesión, redirigir al login
        $rut = $request->session()->get('usuario_rut');
        if (! $rut) {
            return redirect()->route('login');
        }

        // Intentar cargar usuario desde DB; si no existe, usar valores en sesión como fallback
        $usuario = Usuario::where('rut', $rut)->first();
        if (! $usuario) {
            $usuario = (object) [
                'nombre' => $request->session()->get('usuario_nombre'),
                'email' => $request->session()->get('usuario_email'),
                'rol_id' => null,
                'estado' => null,
            ];
        }

        // Obtener nombre del rol si aplica
        $rolName = null;
        if (! empty($usuario->rol_id)) {
            $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
        }
        return view('cocina.index', compact('usuario', 'rolName'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}
