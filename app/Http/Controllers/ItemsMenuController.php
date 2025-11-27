<?php

namespace App\Http\Controllers;

use App\Models\Items_Menu;
use Illuminate\Http\Request;
use App\Models\Items_Categoria;


class ItemsMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemsMenu = Items_Menu::with('categoria')->get();
        $categorias = Items_Categoria::all();
        return view('Items_Menu.index', compact('itemsMenu', 'categorias'));
    }

    /**
     * Muestra la carta pública con los ítems disponibles agrupados por categoría.
     */
    public function verCarta()
    {
        $categorias = Items_Categoria::whereHas('items', function ($q) {
                $q->where('estado', 'disponible');
            })
            ->with(['items' => function ($q) {
                $q->where('estado', 'disponible');
            }])
            ->get();

        return view('Items_Menu.ver_menu', compact('categorias'));
    }

    /**
     * Return items by category (AJAX)
     */
    public function byCategoria($categoria)
    {
        if ($categoria === 'all') {
            $items = Items_Menu::with('categoria')->get();
        } else {
            $items = Items_Menu::with('categoria')->where('categoria_id', $categoria)->get();
        }

        return response()->json($items);
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
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:items__categorias,id',
            'estado' => 'required|in:disponible,no_disponible'
        ]);

        try {
            $item = new Items_Menu();
            $item->nombre = $data['nombre'];
            $item->descripcion = $data['descripcion'];
            $item->precio = $data['precio'];
            $item->categoria_id = $data['categoria_id'];
            $item->estado = $data['estado'];
            $item->restaurante_id = 1;
            $item->save();

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
        return response()->json($items_Menu->load('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Items_Menu $items_Menu)
    {
        return response()->json($items_Menu->load('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Items_Menu $items_Menu)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:items__categorias,id',
            'estado' => 'required|in:disponible,no_disponible'
        ]);

        try {
            $items_Menu->nombre = $data['nombre'];
            $items_Menu->descripcion = $data['descripcion'];
            $items_Menu->precio = $data['precio'];
            $items_Menu->categoria_id = $data['categoria_id'];
            $items_Menu->estado = $data['estado'];
            $items_Menu->save();

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
}
