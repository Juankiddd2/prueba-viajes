<?php
namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Response;

class CiudadController extends Controller
{
    /**
     * Devuelve la lista de ciudades disponibles.
     */
    public function index()
    {
        $ciudades = Ciudad::all();
        return response()->json($ciudades);
    }
}
