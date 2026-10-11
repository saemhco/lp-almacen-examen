<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea todas las rutas hasta que $proyectoListo sea true.
 */
class EnsureProyectoListo
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (config('proyecto.listo') === true) {
            return $next($request);
        }

        return $this->errorResponse(
            'El proyecto no está listo. Abre app/Providers/AppServiceProvider.php y pon $proyectoListo en true.',
            403
        );
    }
}
