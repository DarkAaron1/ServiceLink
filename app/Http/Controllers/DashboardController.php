<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Restaurante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Comanda;
use App\Models\Mesas;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Si hay sesión de Usuario o Empleado, mostrar dashboard; si no, redirigir al selector de login
        $usuario = null;
        $rolName = null;

        $restauranteId = null;
        $rutSesion = $request->session()->get('usuario_rut');
        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        // Preferir usuario (cliente) si existe
        $usuarioRut = $request->session()->get('usuario_rut');
        $empleadoRut = $request->session()->get('empleado_rut');

        if ($usuarioRut) {
            $usuario = Usuario::where('rut', $usuarioRut)->first();
            if (! $usuario) {
                $usuario = (object) [
                    'nombre' => $request->session()->get('usuario_nombre'),
                    'email' => $request->session()->get('usuario_email'),
                    'rol_id' => null,
                    'estado' => null,
                ];
            }

            if (! empty($usuario->rol_id)) {
                $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
            }
        } elseif ($empleadoRut) {
            // Mostrar dashboard para empleado
            $empleado = \App\Models\Empleado::where('rut', $empleadoRut)->first();
            if (! $empleado) {
                $usuario = (object) [
                    'nombre' => $request->session()->get('empleado_nombre'),
                    'email' => $request->session()->get('empleado_email'),
                    'rol_id' => null,
                    'estado' => null,
                ];
                $rolName = $request->session()->get('empleado_cargo');
            } else {
                $usuario = $empleado;
                $rolName = $empleado->cargo ?? null;
            }
        } else {
            return redirect()->route('login');
        }

        $mostSoldProduct = DB::table('pedidos')
            ->select('item_id', DB::raw('COUNT(*) as total_sold'))
            ->groupBy('item_id')
            ->orderByDesc('total_sold')
            ->limit(1)
            ->value('item_id');
        $nameSoldProduct = null;
        if ($mostSoldProduct) {
            $item = DB::table('items__menus')->where('id', $mostSoldProduct)->first();
            if ($item) {
                $nameSoldProduct = $item->nombre;
            } else {
                $nameSoldProduct = "Producto desconocido";
            }
        } else {
            $nameSoldProduct = "Ningún producto vendido aún";
        }

        $ventas = DB::table('pedidos')->count();
        if (! $ventas) {
            $Solds = 'No hay Ventas Registradas';
        }else{
            $Solds = $ventas;
        }

        $ingresos = DB::table('pedidos')->where('created_at', '>=', now()->startOfMonth())->sum('valor_item_ATM');
        if (! $ingresos) {
            $IngresosTotales = 'No hay Ingresos Registrados';
        }else{
            $IngresosTotales = '$ ' . number_format($ingresos, 0, ',', '.');
        }

        $empleados = Empleado::where('restaurante_id', $restauranteId)->get();

        $mesas = Mesas::where('restaurante_id', $restauranteId)->get();

        $comandas = Comanda::whereIn('mesa_id', $mesas->pluck('id')) // Usar whereIn para arrays
    ->with('mesa', 'pedidos.item')
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

        return view('Demo.index', compact('usuario','comandas', 'rolName', 'nameSoldProduct' , 'Solds', 'IngresosTotales', 'empleados'));
    }
}
