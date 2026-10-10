<?php

namespace App\Http\Requests;

class StorePermissionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/',
            'description' => 'nullable|string|max:255',
        ];
    }
}
