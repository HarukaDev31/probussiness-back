<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('api_key');
        $validApiKey = env('API_KEY'); // Puedes almacenar la API key en el archivo .env

        // Verificar el encabezado Origin
        $origin = $request->header('Origin');
        $appUrl = config('app.url');
        $validOrigins = [$appUrl];

        if ($apiKey !== $validApiKey) {
            return response()->json(['api_key' => $apiKey, 'valid_api_key' => $validApiKey]);
            return response()->json(['error' => 'Unauthorized'], 401); // Respuesta en caso de API key inválida
        }

        if (!in_array($origin, $validOrigins)) {
            return response()->json(['error' => 'Forbidden'], 403); // Respuesta en caso de origen no permitido
        }

        return $next($request);
    }
}
