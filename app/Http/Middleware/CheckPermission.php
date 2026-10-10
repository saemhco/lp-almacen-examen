<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige que el usuario autenticado tenga el permiso de la ruta, por ejemplo users.view.
 */
class CheckPermission
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasPermission($permission)) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }
}
