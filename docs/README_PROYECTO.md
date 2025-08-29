# Documentación del proyecto — Prueba Viajes

Este documento resume el código desarrollado y propone un flujo de versionamiento y buenas prácticas para colaborar.

## 1) Resumen rápido
- Stack: Laravel (backend) + MySQL + Angular (frontend)
- Propósito: Permitir seleccionar una ciudad, mostrar el clima actual y convertir un presupuesto en COP a la moneda local del destino.

## 2) Estructura del repositorio
- `/backend` — Laravel app (migrations, seeders, controladores, modelos).
- `/frontend` — Angular  (componentes,vista)

## 3) Backend — puntos importantes
- Rutas principales (archivo `backend/routes/api.php`):
  - `GET /api/ciudades` — lista de ciudades.
  - `POST /api/consulta` — recibe `{ ciudad_id, presupuesto_cop }`, obtiene clima y tasa de cambio, guarda la consulta en BD y devuelve el resultado.
- Controlador central: `backend/app/Http/Controllers/ConsultaController.php`.
- Variables de entorno en `backend/.env` (necesarias):
  - `DB_*` — configuración de MySQL (DB_DATABASE debe ser `viajes` si sigues la convención usada).
  - `OPENWEATHER_API_KEY` — API Key para OpenWeatherMap.
  - `FASTFOREX_API_KEY` — API Key para FastForex (si se usa).

### Cómo correr el backend localmente
1. Instalar dependencias PHP/Composer:

```powershell
cd backend
composer install
```

2. Configurar `.env` con credenciales (clona `.env.example` si existe) y crear la base de datos MySQL `viajes`.

3. Ejecutar migraciones y seeders:

```powershell
php artisan migrate --seed
```

4. Iniciar servidor de desarrollo:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

## 4) Frontend — puntos importantes
- Archivos claves:
  - `frontend/src/app/components/seleccion.component.*` — formulario de entrada.
  - `frontend/src/app/components/resultado.component.*` — vista de resultado.
  - `frontend/src/assets/bg-canvas.js` — script que dibuja el fondo animado.
  - `frontend/src/styles.css` — estilos globales y variables.

### Cómo correr el frontend localmente
1. Instalar dependencias (desde la carpeta `frontend`):

```powershell
cd frontend
npm install
```

2. Iniciar servidor de desarrollo Angular:

```powershell
npm start
```

Por defecto el frontend espera que el backend esté en `http://127.0.0.1:8000/api`. Ajusta `ApiService` si tu backend corre en otra URL.

## 5) Puntos técnicos y notas de diseño
- La conversión de moneda usa FastForex si `FASTFOREX_API_KEY` está presente; el controlador gestiona errores de proveedor y devuelve `tasa_origen` en la respuesta.
- Input de presupuesto se normaliza en el cliente (quita separadores de miles, convierte comas a punto) para manejar formatos como `500.000`.
- El fondo animado se implementó con `frontend/src/assets/bg-canvas.js`. Es autocontenido y respeta `prefers-reduced-motion`.

