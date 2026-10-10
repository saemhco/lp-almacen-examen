<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Http\Requests\UpdateMovementRequest;
use App\Models\Movement;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class MovementController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/movements',
        summary: 'List movements',
        description: 'Permission: movements.view',
        tags: ['Movements'],
        responses: [
            new OA\Response(response: 200, description: 'Movements retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/MovementListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $movements = Movement::all();

        return $this->successResponse($movements, 'Movements retrieved successfully');
    }

    #[OA\Post(
        path: '/api/movements',
        summary: 'Create a movement',
        description: 'Permission: movements.create',
        tags: ['Movements'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['item_id', 'user_id', 'type', 'quantity'],
                properties: [
                    new OA\Property(property: 'item_id', type: 'integer'),
                    new OA\Property(property: 'user_id', type: 'integer'),
                    new OA\Property(property: 'type', type: 'string', enum: ['entrada', 'salida']),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1),
                    new OA\Property(property: 'note', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Movement created successfully', content: new OA\JsonContent(ref: '#/components/schemas/MovementResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreMovementRequest $request)
    {
        try {
            $movement = Movement::create($request->validated());

            return $this->successResponse($movement, 'Movement created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/movements/{movement}',
        summary: 'Show a movement',
        description: 'Permission: movements.view',
        tags: ['Movements'],
        parameters: [
            new OA\Parameter(name: 'movement', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Movement retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/MovementResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($movement)
    {
        try {
            $data = Movement::find($movement);
            if (! $data) {
                return $this->notFoundResponse('Movement not found');
            }

            return $this->successResponse($data, 'Movement retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/movements/{movement}',
        summary: 'Update a movement',
        description: 'Permission: movements.update',
        tags: ['Movements'],
        parameters: [
            new OA\Parameter(name: 'movement', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'item_id', type: 'integer'),
                    new OA\Property(property: 'user_id', type: 'integer'),
                    new OA\Property(property: 'type', type: 'string', enum: ['entrada', 'salida']),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1),
                    new OA\Property(property: 'note', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Movement updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/MovementResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateMovementRequest $request, Movement $movement)
    {
        try {
            $movement->update($request->validated());

            return $this->successResponse($movement, 'Movement updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/movements/{movement}',
        summary: 'Delete a movement',
        description: 'Permission: movements.delete',
        tags: ['Movements'],
        parameters: [
            new OA\Parameter(name: 'movement', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Movement deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Movement $movement)
    {
        $movement->delete();

        return $this->successResponse(null, 'Movement deleted successfully');
    }
}
