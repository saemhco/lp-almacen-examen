<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    #[OA\Get(
        path: '/api/categories',
        summary: 'List categories',
        description: 'Permission: categories.view',
        tags: ['Categories'],
        responses: [
            new OA\Response(response: 200, description: 'Categories retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/CategoryListResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        $categories = Category::all();

        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Create a category',
        description: 'Permission: categories.create',
        tags: ['Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Category created successfully', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());

            return $this->successResponse($category, 'Category created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        summary: 'Show a category',
        description: 'Permission: categories.view',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category retrieved successfully', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function show($category)
    {
        try {
            $data = Category::find($category);
            if (! $data) {
                return $this->notFoundResponse('Category not found');
            }

            return $this->successResponse($data, 'Category retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        summary: 'Update a category',
        description: 'Permission: categories.update',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Category updated successfully', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError'),
        ]
    )]
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());

            return $this->successResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        summary: 'Delete a category',
        description: 'Permission: categories.delete',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthenticated'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }
}
