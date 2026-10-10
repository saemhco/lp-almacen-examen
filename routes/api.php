<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/items', [ItemController::class, 'index'])->middleware('permission:items.view');
    Route::post('/items', [ItemController::class, 'store'])->middleware('permission:items.create');
    Route::get('/items/{item}', [ItemController::class, 'show'])->middleware('permission:items.view');
    Route::put('/items/{item}', [ItemController::class, 'update'])->middleware('permission:items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->middleware('permission:items.delete');

    Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:categories.view');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:categories.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('permission:categories.view');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete');

    Route::get('/suppliers', [SupplierController::class, 'index'])->middleware('permission:suppliers.view');
    Route::post('/suppliers', [SupplierController::class, 'store'])->middleware('permission:suppliers.create');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->middleware('permission:suppliers.view');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('permission:suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:suppliers.delete');

    Route::get('/locations', [LocationController::class, 'index'])->middleware('permission:locations.view');
    Route::post('/locations', [LocationController::class, 'store'])->middleware('permission:locations.create');
    Route::get('/locations/{location}', [LocationController::class, 'show'])->middleware('permission:locations.view');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->middleware('permission:locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->middleware('permission:locations.delete');

    Route::get('/movements', [MovementController::class, 'index'])->middleware('permission:movements.view');
    Route::post('/movements', [MovementController::class, 'store'])->middleware('permission:movements.create');
    Route::get('/movements/{movement}', [MovementController::class, 'show'])->middleware('permission:movements.view');
    Route::put('/movements/{movement}', [MovementController::class, 'update'])->middleware('permission:movements.update');
    Route::delete('/movements/{movement}', [MovementController::class, 'destroy'])->middleware('permission:movements.delete');

    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:users.view');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete');

    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:roles.view');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete');

    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permissions.view');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:permissions.create');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:permissions.view');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete');
});
