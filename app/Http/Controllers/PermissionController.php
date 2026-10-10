<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/permissions',
        summary: 'List permissions',
        description: 'Permission: permissions.view',
        tags: ['Permissions'],
        responses: [
            new OA\Response(response: 200, description: 'Permissions retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/PermissionListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $permissions = Permission::query()->orderBy('name')->get();

        return $this->successResponse($permissions, 'Permissions retrieved successfully');
    }

    #[OA\Post(
        path: '/api/permissions',
        summary: 'Create a permission',
        description: 'Permission: permissions.create. The name uses the form resource.action, for example items.view.',
        tags: ['Permissions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'items.view'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Permission created successfully', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = Permission::query()->create($request->validated());

            return $this->successResponse($permission, 'Permission created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/permissions/{permission}',
        summary: 'Show a permission',
        description: 'Permission: permissions.view',
        tags: ['Permissions'],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Permission retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($permission)
    {
        try {
            $data = Permission::query()->find($permission);
            if (! $data) {
                return $this->notFoundResponse('Permission not found');
            }

            return $this->successResponse($data, 'Permission retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/permissions/{permission}',
        summary: 'Update a permission',
        description: 'Permission: permissions.update',
        tags: ['Permissions'],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'items.view'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Permission updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/PermissionResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        try {
            $permission->update($request->validated());

            return $this->successResponse($permission, 'Permission updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/permissions/{permission}',
        summary: 'Delete a permission',
        description: 'Permission: permissions.delete',
        tags: ['Permissions'],
        parameters: [
            new OA\Parameter(name: 'permission', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Permission deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return $this->successResponse(null, 'Permission deleted successfully');
    }
}
