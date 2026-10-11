<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Attributes as OA;

class FlagController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/mensaje',
        summary: 'Mensaje para el almacenista',
        description: 'GET en JSON. Sin token indica que uses el tuyo. Con el token de un usuario almacenista, el flag está en data.respuesta.',
        tags: ['Mensaje'],
        responses: [
            new OA\Response(response: 200, description: 'El flag es data.respuesta', content: new OA\JsonContent(ref: '#/components/schemas/MensajeResponse')),
            new OA\Response(response: 401, description: 'Falta el token', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'El token no es de un almacenista', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function mensaje(Request $request)
    {
        $plainToken = $request->bearerToken();
        $accessToken = $plainToken ? PersonalAccessToken::findToken($plainToken) : null;
        $user = $accessToken?->tokenable;

        if (! $user instanceof User) {
            return $this->unauthenticatedResponse('Usa tu token para poder ver el mensaje.');
        }

        $user->load('role');

        if ($user->role?->name !== 'almacenista') {
            return $this->forbiddenResponse('Usa el token de un usuario almacenista para poder ver el mensaje.');
        }

        return $this->successResponse([
            'respuesta' => 'FLAG{token-almacenista}',
        ], 'El flag es data.respuesta.');
    }

    public function admin()
    {
        $admin = Role::query()->where('name', 'admin')->first();

        if (! $admin) {
            return $this->sinDatos();
        }

        $cantidad = $admin->permissions()->count();

        return view('flag', [
            'mensaje' => 'Este flag se calcula: es la cantidad de permisos del rol admin. Copia el flag con su formato.',
            'flag' => 'FLAG{'.$cantidad.'}',
        ]);
    }

    public function supervisor()
    {
        $supervisor = Role::query()->where('name', 'supervisor')->first();
        $staff = Role::query()->where('name', 'almacenista')->first();

        if (! $supervisor || ! $staff) {
            return $this->sinDatos();
        }

        $flag = $supervisor->permissions()->pluck('name')
            ->diff($staff->permissions()->pluck('name'))
            ->sort()
            ->values()
            ->implode(',');

        return view('flag', [
            'mensaje' => 'Este flag se calcula: son los permisos que solo tiene el supervisor, en orden alfabético, separados por una coma y sin espacios. Copia el texto.',
            'flag' => $flag,
        ]);
    }

    private function sinDatos()
    {
        return view('flag', [
            'mensaje' => 'Aún no se puede calcular. Ejecuta los seeders con php artisan db:seed y vuelve a abrir esta página.',
            'flag' => null,
        ]);
    }
}
