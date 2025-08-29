# API de Viajes

Esta API permite consultar el clima y la conversión de presupuesto para viajes a ciudades internacionales.

## Endpoints

### Listar ciudades
- **GET** `/api/ciudades`
- Respuesta:
```json
[
  { "id": 1, "nombre": "Londres", "pais": "Reino Unido", "codigo_moneda": "GBP", "simbolo_moneda": "£" },
  ...
]
```

### Realizar consulta
- **POST** `/api/consulta`
- Body:
```json
{
  "ciudad_id": 1,
  "presupuesto_cop": 1000000
}
```
- Respuesta:
```json
{
  "ciudad": "Londres",
  "pais": "Reino Unido",
  "moneda": "GBP",
  "simbolo": "£",
  "clima": 18.5,
  "presupuesto_convertido": 200.5,
  "tasa_cambio": 0.0002
}
```

## APIs externas usadas
- Clima: [OpenWeatherMap](https://openweathermap.org/current)
- Cambio de moneda: [exchangerate.host](https://exchangerate.host/)

## Notas
- Recuerda agregar tu API KEY de OpenWeatherMap en `.env` como `OPENWEATHER_API_KEY`.
- El seeder de ciudades se ejecuta con `php artisan db:seed`.
