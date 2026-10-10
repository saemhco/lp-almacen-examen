<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class SupplierController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/suppliers',
        summary: 'List suppliers',
        description: 'Permission: suppliers.view',
        tags: ['Suppliers'],
        responses: [
            new OA\Response(response: 200, description: 'Suppliers retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/SupplierListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $suppliers = Supplier::all();

        return $this->successResponse($suppliers, 'Suppliers retrieved successfully');
    }

    #[OA\Post(
        path: '/api/suppliers',
        summary: 'Create a supplier',
        description: 'Permission: suppliers.create',
        tags: ['Suppliers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
                    new OA\Property(property: 'phone', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Supplier created successfully', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreSupplierRequest $request)
    {
        try {
            $supplier = Supplier::create($request->validated());

            return $this->successResponse($supplier, 'Supplier created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/suppliers/{supplier}',
        summary: 'Show a supplier',
        description: 'Permission: suppliers.view',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($supplier)
    {
        try {
            $data = Supplier::find($supplier);
            if (! $data) {
                return $this->notFoundResponse('Supplier not found');
            }

            return $this->successResponse($data, 'Supplier retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/suppliers/{supplier}',
        summary: 'Update a supplier',
        description: 'Permission: suppliers.update',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
                    new OA\Property(property: 'phone', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Supplier updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        try {
            $supplier->update($request->validated());

            return $this->successResponse($supplier, 'Supplier updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/suppliers/{supplier}',
        summary: 'Delete a supplier',
        description: 'Permission: suppliers.delete',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return $this->successResponse(null, 'Supplier deleted successfully');
    }
}
