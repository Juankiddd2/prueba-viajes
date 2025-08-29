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

            // Obtener tasa de cambio (exchangerate.host)
            $exchangeUrl = "https://api.exchangerate.host/convert?from=COP&to={$ciudad->codigo_moneda}";
            $exchangeResponse = Http::get($exchangeUrl);

            if (! $exchangeResponse->ok()) {
                return response()->json([
                    'error' => 'Error obteniendo tasa de cambio',
                    'status' => $exchangeResponse->status(),
                    'body' => $exchangeResponse->body()
                ], 502);
            }

            $tasa = $exchangeResponse->json('info.rate');
            // fallback: si no viene info.rate, intentar derivarla de result/query.amount
            if ($tasa === null) {
                $result = $exchangeResponse->json('result');
                $amount = $exchangeResponse->json('query.amount') ?? 1;
                if ($result !== null && $amount != 0) {
                    $tasa = $result / $amount;
                }
            }

            // Segundo fallback: usar endpoint /latest para obtener la tasa directamente
            if ($tasa === null) {
                $exchangeUrl2 = "https://api.exchangerate.host/latest?base=COP&symbols={$ciudad->codigo_moneda}";
                $exchangeResponse2 = Http::get($exchangeUrl2);
                if ($exchangeResponse2->ok()) {
                    $tasa = $exchangeResponse2->json("rates.{$ciudad->codigo_moneda}");
                }
            }

            // Último recurso: fallback local (tasas aproximadas) para desarrollo/demo o una falla de la api
            $fromFallback = false;
            if ($tasa === null) {
                $fallbackRates = [
                    'USD' => 0.00027, // 1 COP = 0.00027 USD (~1 USD = 3700 COP)
                    'GBP' => 0.00022,
                    'EUR' => 0.00025,
                    'JPY' => 0.037,
                    'MAD' => 0.0029,
                ];

                if (isset($fallbackRates[$ciudad->codigo_moneda])) {
                    $tasa = $fallbackRates[$ciudad->codigo_moneda];
                    $fromFallback = true;
                   
                    logger()->warning('Usando tasa de cambio fallback para ' . $ciudad->codigo_moneda);
                }
            }

            if ($tasa === null) {
                return response()->json(['error' => 'No se pudo obtener la tasa de cambio desde el proveedor'], 502);
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
                'tasa_origen' => $fromFallback ? 'fallback' : 'api',
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
