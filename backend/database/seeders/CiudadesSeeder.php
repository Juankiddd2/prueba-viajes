<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ciudades')->insert([
            ['nombre' => 'Londres', 'pais' => 'Reino Unido', 'codigo_moneda' => 'GBP', 'simbolo_moneda' => '£'],
            ['nombre' => 'New York', 'pais' => 'Estados Unidos', 'codigo_moneda' => 'USD', 'simbolo_moneda' => '$'],
            ['nombre' => 'Paris', 'pais' => 'Francia', 'codigo_moneda' => 'EUR', 'simbolo_moneda' => '€'],
            ['nombre' => 'Tokyo', 'pais' => 'Japón', 'codigo_moneda' => 'JPY', 'simbolo_moneda' => '¥'],
            ['nombre' => 'Madrid', 'pais' => 'España', 'codigo_moneda' => 'EUR', 'simbolo_moneda' => '€'],
        ]);
    }
}
