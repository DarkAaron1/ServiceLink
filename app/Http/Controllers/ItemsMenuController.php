<?php

namespace App\Http\Controllers;

use App\Models\Items_Menu;
use Illuminate\Http\Request;
use App\Models\Items_Categoria;
use App\Models\Restaurante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;


class ItemsMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Intenta obtener el restaurante desde sesión/autenticación para el admin.
        $restauranteId = $this->getContextRestauranteId();

        if ($restauranteId) {
            $itemsMenu = Items_Menu::with('categoria')->where('restaurante_id', $restauranteId)->get();
            $categorias = Items_Categoria::where('restaurante_id', $restauranteId)->get();
        } else {
            // fallback: mostrar todos si no hay contexto
            $itemsMenu = Items_Menu::with('categoria')->get();
            $categorias = Items_Categoria::all();
        }
        // Datos del actor (Usuario o Empleado)
        $actor = $this->getActor($request);
        if (! $actor) {
            return redirect()->route('login');
        }

        $usuario = $actor['model'] ?? (object) ['nombre' => $actor['nombre'], 'email' => $actor['email']];
        $rolName = $actor['rolName'] ?? null;

        // Si no hay restaurante en contexto, intentar usar el restaurante del empleado si aplica
        if (! $this->getContextRestauranteId() && isset($actor['restaurante_id']) && $actor['restaurante_id']) {
            $restauranteId = $actor['restaurante_id'];
            $itemsMenu = Items_Menu::with('categoria')->where('restaurante_id', $restauranteId)->get();
            $categorias = Items_Categoria::where('restaurante_id', $restauranteId)->get();
        }

        // añadir imagen_url para consumo en la vista
        $itemsMenu->each(function ($it) {
            $it->imagen_url = $this->resolveImageUrl($it->imagen);
        });


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


        return view('Items_Menu.index', compact('itemsMenu', 'categorias','usuario', 'rolName'));
    }

    /**
     * Muestra la carta pública con los ítems disponibles agrupados por categoría.
     */
    public function verCarta($restauranteId)
    {
        // Cargar restaurante y validar
        $restaurante = Restaurante::findOrFail($restauranteId);

        // Mostrar solo categorias vinculadas al restaurante (o globales si las conserva)
        $categorias = Items_Categoria::where(function ($q) use ($restauranteId) {
                $q->where('restaurante_id', $restauranteId)->orWhereNull('restaurante_id');
            })
            ->whereHas('items', function ($q) use ($restauranteId) {
                $q->where('estado', 'disponible')->where('restaurante_id', $restauranteId);
            })
            ->with(['items' => function ($q) use ($restauranteId) {
                $q->where('estado', 'disponible')->where('restaurante_id', $restauranteId);
            }])
            ->get();

        // añadir imagen_url a cada item dentro de cada categoría
        $categorias->each(function ($cat) {
            if ($cat->relationLoaded('items') && $cat->items) {
                $cat->items->each(function ($it) {
                    $it->imagen_url = $this->resolveImageUrl($it->imagen);
                });
            }
        });

        return view('Items_Menu.ver_menu', compact('categorias', 'restaurante'));
    }

    /**
     * Return items by category (AJAX)
     */
    public function byCategoria($categoria)
    {
        $restauranteId = $this->getContextRestauranteId();

        if ($categoria === 'all') {
            $query = Items_Menu::with('categoria');
        } else {
            $query = Items_Menu::with('categoria')->where('categoria_id', $categoria);
        }

        if ($restauranteId) {
            $query->where('restaurante_id', $restauranteId);
        }

        $items = $query->get();

        // exponer imagen_url en JSON
        $items->each(function ($it) {
            $it->imagen_url = $this->resolveImageUrl($it->imagen);
        });

        return response()->json($items);
    }

    /**
     * Determine restaurant context for admin actions.
     * It tries session('usuario_rut') then Auth::user() rut, and finds the Restaurante where rut_admin matches.
     */
    protected function getContextRestauranteId()
    {
        $rutSesion = request()->session()->get('usuario_rut');

        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) return $rest->id;
        }

        $userAuth = Auth::user();
        if ($userAuth && isset($userAuth->rut)) {
            $rest = Restaurante::where('rut_admin', $userAuth->rut)->first();
            if ($rest) return $rest->id;
        }

        return null;
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
        // validar categoría admitiendo solo categorías del restaurante en contexto (si existe)
        $restauranteIdForValidation = $this->getContextRestauranteId() ?? ($request->filled('restaurante_id') ? $request->input('restaurante_id') : null);

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'estado' => 'required|in:disponible,no_disponible'
            
        ];

        if ($restauranteIdForValidation) {
            $rules['categoria_id'] = 'required|exists:items__categorias,id,restaurante_id,' . $restauranteIdForValidation;
        } else {
            $rules['categoria_id'] = 'required|exists:items__categorias,id';
        }

        $data = $request->validate($rules);

        try {
            $item = new Items_Menu();
            $item->nombre = $data['nombre'];
            $item->descripcion = $data['descripcion'];
            $item->precio = $data['precio'];
            $item->categoria_id = $data['categoria_id'];
            $item->estado = $data['estado'];

            $restauranteId = $this->getContextRestauranteId();
            if (! $restauranteId && $request->filled('restaurante_id')) {
                $restauranteId = $request->input('restaurante_id');
            }

            if (! $restauranteId) {
                throw new \Exception('No se pudo determinar el restaurante para el item.');
            }

            $item->restaurante_id = $restauranteId;

            // manejar imagen (opcional)
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('items', 'public');
                $item->imagen = $path; // ruta relativa en storage (ej: items/abcd.jpg)
            }

            $item->save();

            // añadir imagen_url antes de devolver
            $item->imagen_url = $this->resolveImageUrl($item->imagen);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item creado exitosamente',
                    'item' => $item
                ]);
            }

            return redirect()->route('items_menu.index')->with('success', 'Item creado exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el item',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al crear el item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Items_Menu $items_Menu)
    {
        $items_Menu->imagen_url = $this->resolveImageUrl($items_Menu->imagen);
        return response()->json($items_Menu->load('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Items_Menu $items_Menu)
    {
        $items_Menu->imagen_url = $this->resolveImageUrl($items_Menu->imagen);
        return response()->json($items_Menu->load('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items_Menu $items_Menu)
    {
        $restauranteIdForValidation = $this->getContextRestauranteId();

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,no_disponible',
            'imagen' => 'nullable|image|max:2048'
        ];

        if ($restauranteIdForValidation) {
            $rules['categoria_id'] = 'required|exists:items__categorias,id,restaurante_id,' . $restauranteIdForValidation;
        } else {
            $rules['categoria_id'] = 'required|exists:items__categorias,id';
        }

        $data = $request->validate($rules);

        try {
            $items_Menu->nombre = $data['nombre'];
            $items_Menu->descripcion = $data['descripcion'];
            $items_Menu->precio = $data['precio'];
            $items_Menu->categoria_id = $data['categoria_id'];
            $items_Menu->estado = $data['estado'];

            // si se sube nueva imagen, eliminar la anterior (si está en storage 'public') y guardar la nueva
            if ($request->hasFile('imagen')) {
                if ($items_Menu->imagen && ! Str::startsWith($items_Menu->imagen, ['http://','https://','/'])) {
                    Storage::disk('public')->delete($items_Menu->imagen);
                }
                $path = $request->file('imagen')->store('items', 'public');
                $items_Menu->imagen = $path;
            }

            $items_Menu->save();

            // añadir imagen_url antes de devolver
            $items_Menu->imagen_url = $this->resolveImageUrl($items_Menu->imagen);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item actualizado exitosamente',
                    'item' => $items_Menu
                ]);
            }

            return redirect()->route('items_menu.index')->with('success', 'Item actualizado exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el item',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al actualizar el item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Items_Menu $items_menu)
    {
        try {
            // Si hay contexto administrador, asegurar que el item pertenece al mismo restaurante
            $contextRest = $this->getContextRestauranteId();
            if ($contextRest && $items_menu->restaurante_id && $items_menu->restaurante_id != $contextRest) {
                throw new \Exception('No autorizado para eliminar este item');
            }

            // borrar imagen almacenada en disk 'public' si corresponde
            if ($items_menu->imagen && ! Str::startsWith($items_menu->imagen, ['http://','https://','/'])) {
                Storage::disk('public')->delete($items_menu->imagen);
            }

            $items_menu->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item eliminado exitosamente'
                ]);
            }

            return redirect()->route('items_menu.index')->with('success', 'Item eliminado exitosamente.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el item',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al eliminar el item: ' . $e->getMessage());
        }
    }

    /**
     * Resolve public URL for stored image or return original url if absolute or starts with '/'
     */
    protected function resolveImageUrl($path)
    {
        if (empty($path)) return null;
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }
        return Storage::url($path);
    }
}
