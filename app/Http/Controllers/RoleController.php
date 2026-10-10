<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class RoleController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/roles',
        summary: 'List roles',
        description: 'Permission: roles.view',
        tags: ['Roles'],
        responses: [
            new OA\Response(response: 200, description: 'Roles retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/RoleListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $roles = Role::query()->with('permissions')->get();

        return $this->successResponse($roles, 'Roles retrieved successfully');
    }

    #[OA\Post(
        path: '/api/roles',
        summary: 'Create a role',
        description: 'Permission: roles.create. permission_ids assigns the permissions.',
        tags: ['Roles'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(
                        property: 'permission_ids',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Role created successfully', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreRoleRequest $request)
    {
        try {
            $validated = $request->validated();

            $role = Role::query()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->exists('permission_ids')) {
                $role->permissions()->sync($validated['permission_ids'] ?? []);
            }

            return $this->successResponse($role->load('permissions'), 'Role created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/roles/{role}',
        summary: 'Show a role',
        description: 'Permission: roles.view',
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Role retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($role)
    {
        try {
            $data = Role::query()->with('permissions')->find($role);
            if (! $data) {
                return $this->notFoundResponse('Role not found');
            }

            return $this->successResponse($data, 'Role retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/roles/{role}',
        summary: 'Update a role',
        description: 'Permission: roles.update. permission_ids replaces the role permissions.',
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(
                        property: 'permission_ids',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Role updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/RoleResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $validated = $request->validated();

            $role->update([
                'name' => $validated['name'] ?? $role->name,
                'description' => array_key_exists('description', $validated)
                    ? $validated['description']
                    : $role->description,
            ]);

            if ($request->exists('permission_ids')) {
                $role->permissions()->sync($validated['permission_ids'] ?? []);
            }

            return $this->successResponse($role->load('permissions'), 'Role updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/roles/{role}',
        summary: 'Delete a role',
        description: 'Permission: roles.delete. Fails with 409 if the role still has users.',
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Role deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 409, ref: '#/components/responses/Conflict'),
        ]
    )]
    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return $this->errorResponse('Role has users and cannot be deleted', 409);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }
}
