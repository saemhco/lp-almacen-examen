<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes as OA;

class RegistroController extends Controller
{
    use ApiResponseTrait;

    #[OA\Post(
        path: '/api/registro',
        summary: 'Registrar al estudiante y obtener el flag',
        description: 'POST. También se puede llamar desde Postman con el token de un administrador. Cuerpo: nombres, apellidos y codigo_universitario. El servidor agrega los metadatos del equipo y consulta el servicio externo, que no pide sesión. Si el rol no coincide, el mensaje indica el archivo y qué corregir. La respuesta está en data.mensaje.',
        tags: ['Registro'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombres', 'apellidos', 'codigo_universitario'],
                properties: [
                    new OA\Property(property: 'nombres', type: 'string', example: 'Saúl'),
                    new OA\Property(property: 'apellidos', type: 'string', example: 'Escandón'),
                    new OA\Property(property: 'codigo_universitario', type: 'string', example: '20201234'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Registro correcto. La respuesta está en data.mensaje.',
                content: new OA\JsonContent(ref: '#/components/schemas/RegistroResponse')
            ),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, description: 'El mensaje indica el archivo y qué corregir', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(
                response: 502,
                description: 'El servicio externo no devolvió el código',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiError')
            ),
        ]
    )]
    public function store(StoreRegistroRequest $request)
    {
        $user = $request->user()->load('role');
        $rol = config('udh.rol');

        if ($user->role?->name !== 'admin') {
            return $this->forbiddenResponse('Usa el token de un usuario administrador.');
        }

        if ($user->role->name !== $rol) {
            return $this->forbiddenResponse('El nombre del rol es incorrecto. Abre config/udh.php y corrige el nombre del rol.');
        }

        $validated = $request->validated();

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->post(config('udh.url'), [
                    'nombres' => $validated['nombres'],
                    'apellidos' => $validated['apellidos'],
                    'codigo_universitario' => $validated['codigo_universitario'],
                    'metadatos' => array_merge(config('udh.metadatos'), [
                        'ip' => $request->ip(),
                        'agente' => $request->userAgent(),
                        'fecha' => now()->format('Y-m-d'),
                    ]),
                ]);
        } catch (\Exception $e) {
            return $this->errorResponse('No se pudo contactar el servicio de registro', 502);
        }

        $codigo = $response->json('codigo');

        if (! $response->successful() || ! is_string($codigo) || $codigo === '') {
            return $this->errorResponse('No se pudo obtener el código de registro', 502);
        }

        return $this->successResponse([
            'mensaje' => $codigo,
        ], 'La respuesta está en data.mensaje.');
    }
}
