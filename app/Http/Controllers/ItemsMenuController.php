<?php

namespace App\Http\Controllers;

use App\Models\Items_Menu;
use Illuminate\Http\Request;

class ItemsMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemsMenu = Items_Menu::with('categoria')->get();
        $categorias = \App\Models\Items_Categoria::all();
        return view('items_Menu.index', compact('itemsMenu', 'categorias'));
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
            'disponible' => 'required|boolean'
        ]);

        try {
            $item = new Items_Menu();
            $item->nombre = $data['nombre'];
            $item->descripcion = $data['descripcion'];
            $item->precio = $data['precio'];
            $item->items_categoria_id = $data['categoria_id'];
            $item->disponible = $data['disponible'];
            $item->items_restaurante_id = 1; 
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
            'disponible' => 'required|boolean'
        ]);

        try {
            $items_Menu->nombre = $data['nombre'];
            $items_Menu->descripcion = $data['descripcion'];
            $items_Menu->precio = $data['precio'];
            $items_Menu->items_categoria_id = $data['categoria_id'];
            $items_Menu->disponible = $data['disponible'];
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
    public function destroy(Items_Menu $items_Menu)
    {
        try {
            $items_Menu->delete();

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
