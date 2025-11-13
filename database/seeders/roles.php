<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Crear roles predeterminados, y los dropea en caso de que ya existan
        \DB::table('roles')->truncate();
        \DB::table('roles')->insert([
            ['nombre' => 'admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'mesero', 'descripcion' => 'Mesero del restaurante'],
            ['nombre' => 'cocinero', 'descripcion' => 'Cocinero del restaurante'],
            ['nombre' => 'recepcionista', 'descripcion' => 'Recepcionista del restaurante'],
        ]);
    }
}
