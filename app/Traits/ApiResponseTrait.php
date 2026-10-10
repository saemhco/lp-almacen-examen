<?php

namespace App\Traits;

use Throwable;

/**
 * Respuesta JSON estándar de la API.
 * Todas las respuestas usan las mismas claves: success, code, message y data.
 */
trait ApiResponseTrait
{
    public function successResponse($data = null, ?string $message = null, int $code = 200)
    {
        return $this->jsonResponse(true, $message, $data, $code);
    }

    public function errorResponse(string $message, int $code = 400, $errors = null)
    {
        return $this->jsonResponse(false, $message, $errors, $code);
    }

    public function validationErrorResponse($errors)
    {
        return $this->errorResponse('Validation Error', 422, $errors);
    }

    public function unauthenticatedResponse(string $message = 'Unauthenticated')
    {
        return $this->errorResponse($message, 401);
    }

    public function forbiddenResponse(string $message = 'Forbidden')
    {
        return $this->errorResponse($message, 403);
    }

    public function notFoundResponse(string $message)
    {
        return $this->errorResponse($message, 404);
    }

    public function serverErrorResponse(Throwable $exception)
    {
        return $this->errorResponse('Server Error', 500, $exception->getMessage());
    }

    protected function jsonResponse(bool $success, ?string $message, $data, int $code)
    {
        return response()->json([
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
