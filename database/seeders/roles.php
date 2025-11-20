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
        \DB::table('roles')->insert([
            ['nombre' => 'Admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Mesero', 'descripcion' => 'Mesero del restaurante'],
            ['nombre' => 'Cocinero', 'descripcion' => 'Cocinero del restaurante'],
            ['nombre' => 'Recepcionista', 'descripcion' => 'Recepcionista del restaurante'],
        ]);
    }
}
