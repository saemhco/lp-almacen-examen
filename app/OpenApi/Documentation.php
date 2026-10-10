<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'Login y logout. El login es publico y devuelve el token Bearer.')]
#[OA\Tag(name: 'Items', description: 'Productos del almacen. Permisos: items.view, items.create, items.update, items.delete.')]
#[OA\Tag(name: 'Categories', description: 'Categorias. Permisos: categories.view, categories.create, categories.update, categories.delete.')]
#[OA\Tag(name: 'Suppliers', description: 'Proveedores. Permisos: suppliers.view, suppliers.create, suppliers.update, suppliers.delete.')]
#[OA\Tag(name: 'Locations', description: 'Ubicaciones. Permisos: locations.view, locations.create, locations.update, locations.delete.')]
#[OA\Tag(name: 'Movements', description: 'Entradas y salidas. Permisos: movements.view, movements.create, movements.update, movements.delete.')]
#[OA\Tag(name: 'Users', description: 'Usuarios. Permisos: users.view, users.create, users.update, users.delete. Solo el admin los tiene.')]
#[OA\Tag(name: 'Roles', description: 'Roles. Permisos: roles.view, roles.create, roles.update, roles.delete. Solo el admin los tiene.')]
#[OA\Tag(name: 'Permissions', description: 'Permisos. Permisos: permissions.view, permissions.create, permissions.update, permissions.delete. Solo el admin los tiene.')]
#[OA\Schema(
    schema: 'ApiError',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'code', type: 'integer', example: 400),
        new OA\Property(property: 'message', type: 'string', example: 'Forbidden'),
        new OA\Property(property: 'data', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'code', type: 'integer', example: 422),
        new OA\Property(property: 'message', type: 'string', example: 'Validation Error'),
        new OA\Property(
            property: 'data',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'EmptyResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string', example: 'Deleted successfully'),
        new OA\Property(property: 'data', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'Category',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Ferreteria'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Supplier',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Aceros del Norte'),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
        new OA\Property(property: 'phone', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Location',
    required: ['id', 'name', 'code'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Pasillo A'),
        new OA\Property(property: 'code', type: 'string', example: 'A-01'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Item',
    required: ['id', 'name', 'price', 'quantity', 'category_id', 'supplier_id', 'location_id'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Martillo'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'string', example: '25.50'),
        new OA\Property(property: 'quantity', type: 'integer', example: 10),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
        new OA\Property(property: 'location_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Movement',
    required: ['id', 'item_id', 'user_id', 'type', 'quantity'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'item_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', enum: ['entrada', 'salida'], example: 'entrada'),
        new OA\Property(property: 'quantity', type: 'integer', example: 5),
        new OA\Property(property: 'note', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Permission',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'items.view'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Ver items'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'Role',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'admin'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(ref: '#/components/schemas/Permission')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'User',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Admin'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'role_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'role', ref: '#/components/schemas/Role'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'LoginData',
    required: ['token', 'user'],
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|plain-text-token'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
#[OA\Schema(
    schema: 'LoginResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
        new OA\Property(property: 'data', ref: '#/components/schemas/LoginData'),
    ]
)]
#[OA\Schema(
    schema: 'CategoryResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
    ]
)]
#[OA\Schema(
    schema: 'CategoryListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Category')),
    ]
)]
#[OA\Schema(
    schema: 'SupplierResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Supplier'),
    ]
)]
#[OA\Schema(
    schema: 'SupplierListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Supplier')),
    ]
)]
#[OA\Schema(
    schema: 'LocationResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Location'),
    ]
)]
#[OA\Schema(
    schema: 'LocationListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Location')),
    ]
)]
#[OA\Schema(
    schema: 'ItemResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Item'),
    ]
)]
#[OA\Schema(
    schema: 'ItemListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Item')),
    ]
)]
#[OA\Schema(
    schema: 'MovementResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Movement'),
    ]
)]
#[OA\Schema(
    schema: 'MovementListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Movement')),
    ]
)]
#[OA\Schema(
    schema: 'PermissionResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Permission'),
    ]
)]
#[OA\Schema(
    schema: 'PermissionListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Permission')),
    ]
)]
#[OA\Schema(
    schema: 'RoleResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Role'),
    ]
)]
#[OA\Schema(
    schema: 'RoleListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Role')),
    ]
)]
#[OA\Schema(
    schema: 'UserResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', ref: '#/components/schemas/User'),
    ]
)]
#[OA\Schema(
    schema: 'UserListResponse',
    required: ['success', 'code', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/User')),
    ]
)]
#[OA\Response(response: 'Unauthenticated', description: 'Falta el token o no es valido.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError'))]
#[OA\Response(response: 'Forbidden', description: 'El rol no tiene el permiso de esta accion.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError'))]
#[OA\Response(response: 'NotFound', description: 'El registro no existe.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError'))]
#[OA\Response(response: 'ValidationError', description: 'Los datos no cumplen las reglas.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'))]
#[OA\Response(response: 'Conflict', description: 'El registro no se puede borrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError'))]
#[OA\Response(response: 'ServerError', description: 'Error interno.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError'))]
#[OA\Response(response: 'EmptySuccess', description: 'Operacion correcta. data es null.', content: new OA\JsonContent(ref: '#/components/schemas/EmptyResponse'))]
class Documentation {}
