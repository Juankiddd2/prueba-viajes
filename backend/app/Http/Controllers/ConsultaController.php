<?php
namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConsultaController extends Controller
{
    /**
     * Registra una consulta y devuelve clima, moneda y conversión.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'presupuesto_cop' => 'required|numeric|min:1',
        ]);

        $ciudad = Ciudad::findOrFail($request->ciudad_id);

        // Obtener clima (OpenWeatherMap)
        $weatherApiKey = env('OPENWEATHER_API_KEY');
        $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q={$ciudad->nombre}&appid={$weatherApiKey}&units=metric&lang=es";
        $weatherResponse = Http::get($weatherUrl);
        $clima = $weatherResponse->json('main.temp');

        // Obtener tasa de cambio (exchangerate.host)
        $exchangeUrl = "https://api.exchangerate.host/convert?from=COP&to={$ciudad->codigo_moneda}";
        $exchangeResponse = Http::get($exchangeUrl);
        $tasa = $exchangeResponse->json('info.rate');
        $presupuesto_convertido = $request->presupuesto_cop * $tasa;

        // Guardar consulta
        $consulta = Consulta::create([
            'ciudad_id' => $ciudad->id,
            'presupuesto_cop' => $request->presupuesto_cop,
            'presupuesto_convertido' => $presupuesto_convertido,
            'tasa_cambio' => $tasa,
            'clima' => $clima,
        ]);

        return response()->json([
            'ciudad' => $ciudad->nombre,
            'pais' => $ciudad->pais,
            'moneda' => $ciudad->codigo_moneda,
            'simbolo' => $ciudad->simbolo_moneda,
            'clima' => $clima,
            'presupuesto_convertido' => $presupuesto_convertido,
            'tasa_cambio' => $tasa,
        ]);
    }
}
