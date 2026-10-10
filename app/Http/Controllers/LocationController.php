<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class LocationController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/locations',
        summary: 'List locations',
        description: 'Permission: locations.view',
        tags: ['Locations'],
        responses: [
            new OA\Response(response: 200, description: 'Locations retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/LocationListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $locations = Location::all();

        return $this->successResponse($locations, 'Locations retrieved successfully');
    }

    #[OA\Post(
        path: '/api/locations',
        summary: 'Create a location',
        description: 'Permission: locations.create',
        tags: ['Locations'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'code'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'code', type: 'string', maxLength: 255),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Location created successfully', content: new OA\JsonContent(ref: '#/components/schemas/LocationResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreLocationRequest $request)
    {
        try {
            $location = Location::create($request->validated());

            return $this->successResponse($location, 'Location created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/locations/{location}',
        summary: 'Show a location',
        description: 'Permission: locations.view',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Location retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/LocationResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($location)
    {
        try {
            $data = Location::find($location);
            if (! $data) {
                return $this->notFoundResponse('Location not found');
            }

            return $this->successResponse($data, 'Location retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/locations/{location}',
        summary: 'Update a location',
        description: 'Permission: locations.update',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'code', type: 'string', maxLength: 255),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Location updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/LocationResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateLocationRequest $request, Location $location)
    {
        try {
            $location->update($request->validated());

            return $this->successResponse($location, 'Location updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/locations/{location}',
        summary: 'Delete a location',
        description: 'Permission: locations.delete',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'location', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Location deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Location $location)
    {
        $location->delete();

        return $this->successResponse(null, 'Location deleted successfully');
    }
}
