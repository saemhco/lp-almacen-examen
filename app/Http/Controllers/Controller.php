<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Almacen API',
    description: 'API para administrar el almacen. Salvo el login, las rutas piden un token Bearer de Sanctum.'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Token devuelto por POST /api/login'
)]
#[OA\OpenApi(security: [['bearerAuth' => []]])]
abstract class Controller
{
    //
}
