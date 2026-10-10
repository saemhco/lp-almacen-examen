<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/users',
        summary: 'List users',
        description: 'Permission: users.view',
        tags: ['Users'],
        responses: [
            new OA\Response(response: 200, description: 'Users retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/UserListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $users = User::query()->with('role')->get();

        return $this->successResponse($users, 'Users retrieved successfully');
    }

    #[OA\Post(
        path: '/api/users',
        summary: 'Create a user',
        description: 'Permission: users.create',
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'role_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', minLength: 8),
                    new OA\Property(property: 'role_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created successfully', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::query()->create($request->validated());

            return $this->successResponse($user->load('role'), 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/users/{user}',
        summary: 'Show a user',
        description: 'Permission: users.view',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($user)
    {
        try {
            $data = User::query()->with('role')->find($user);
            if (! $data) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse($data, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/users/{user}',
        summary: 'Update a user',
        description: 'Permission: users.update',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', minLength: 8, nullable: true),
                    new OA\Property(property: 'role_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();

            if (blank($validated['password'] ?? null)) {
                unset($validated['password']);
            }

            $user->update($validated);

            return $this->successResponse($user->load('role'), 'User updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/users/{user}',
        summary: 'Delete a user',
        description: 'Permission: users.delete. Fails with 409 if the user is the current session or has movements.',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 409, ref: '#/components/responses/Conflict'),
        ]
    )]
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return $this->errorResponse('You cannot delete your own user', 409);
        }

        if ($user->movements()->exists()) {
            return $this->errorResponse('User has movements and cannot be deleted', 409);
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }
}
