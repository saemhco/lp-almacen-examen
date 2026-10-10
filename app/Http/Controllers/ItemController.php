<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class ItemController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/items',
        summary: 'List items',
        description: 'Permission: items.view',
        tags: ['Items'],
        responses: [
            new OA\Response(response: 200, description: 'Items retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/ItemListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $items = Item::all();

        return $this->successResponse($items, 'Items retrieved successfully');
    }

    #[OA\Post(
        path: '/api/items',
        summary: 'Create an item',
        description: 'Permission: items.create',
        tags: ['Items'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'quantity', 'category_id', 'supplier_id', 'location_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 0),
                    new OA\Property(property: 'category_id', type: 'integer'),
                    new OA\Property(property: 'supplier_id', type: 'integer'),
                    new OA\Property(property: 'location_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Item created successfully', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreItemRequest $request)
    {
        try {
            $item = Item::create($request->validated());

            return $this->successResponse($item, 'Item created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/items/{item}',
        summary: 'Show an item',
        description: 'Permission: items.view',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($item)
    {
        try {
            $data = Item::find($item);
            if (! $data) {
                return $this->notFoundResponse('Item not found');
            }

            return $this->successResponse($data, 'Item retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/items/{item}',
        summary: 'Update an item',
        description: 'Permission: items.update',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 0),
                    new OA\Property(property: 'category_id', type: 'integer'),
                    new OA\Property(property: 'supplier_id', type: 'integer'),
                    new OA\Property(property: 'location_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Item updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateItemRequest $request, Item $item)
    {
        try {
            $item->update($request->validated());

            return $this->successResponse($item, 'Item updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/items/{item}',
        summary: 'Delete an item',
        description: 'Permission: items.delete',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Item $item)
    {
        $item->delete();

        return $this->successResponse(null, 'Item deleted successfully');
    }
}
