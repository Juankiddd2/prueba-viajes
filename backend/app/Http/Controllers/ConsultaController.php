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

        try {
            // Obtener clima (OpenWeatherMap)
            $weatherApiKey = env('OPENWEATHER_API_KEY');
            if (empty($weatherApiKey)) {
                return response()->json(['error' => 'Falta OPENWEATHER_API_KEY en .env'], 500);
            }

            $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q={$ciudad->nombre}&appid={$weatherApiKey}&units=metric&lang=es";
            $weatherResponse = Http::get($weatherUrl);

            if (! $weatherResponse->ok()) {
                return response()->json([
                    'error' => 'Error obteniendo clima',
                    'status' => $weatherResponse->status(),
                    'body' => $weatherResponse->body()
                ], 502);
            }

            $clima = $weatherResponse->json('main.temp');
            if ($clima === null) {
                return response()->json(['error' => 'Respuesta de clima no contiene temperatura (main.temp)'], 502);
            }

           
            $fastKey = env('FASTFOREX_API_KEY');
            $tasa = null;
            $tasa_source = null;

            if (!empty($fastKey)) {
                // usar fetch-multi para solicitar la moneda destino
                $fastUrl = "https://api.fastforex.io/fetch-multi?from=COP&to={$ciudad->codigo_moneda}&api_key={$fastKey}";
                $fastResp = Http::get($fastUrl);

                if (! $fastResp->ok()) {
                    return response()->json([
                        'error' => 'Error obteniendo tasa desde FastForex',
                        'status' => $fastResp->status(),
                        'body' => $fastResp->body(),
                        'tasa_origen' => 'none'
                    ], 502);
                }

            
                $rate = $fastResp->json("results.{$ciudad->codigo_moneda}");
                if ($rate === null) {
                
                    $rate = $fastResp->json("result.{$ciudad->codigo_moneda}") ?? $fastResp->json('result') ?? null;
                }

                if ($rate !== null) {
                    $tasa = $rate;
                    $tasa_source = 'fastforex';
                } else {
                    return response()->json([
                        'error' => 'FastForex respondió pero no contenía tasa para la moneda solicitada',
                        'body' => $fastResp->body(),
                        'tasa_origen' => 'none'
                    ], 502);
                }
            } else {
                return response()->json([
                    'error' => 'No se encontró FASTFOREX_API_KEY en .env; añade FASTFOREX_API_KEY=tu_clave',
                    'tasa_origen' => 'none'
                ], 502);
            }

            $presupuesto_convertido = round($request->presupuesto_cop * $tasa, 2);

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
                'tasa_origen' => $tasa_source ?? 'none',
            ]);

        } catch (\Exception $e) {
            // devolver error legible para debugging en desarrollo
            return response()->json([
                'error' => 'Excepción en servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
