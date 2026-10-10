<?php

namespace App\Http\Requests;

class UpdatePermissionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/|unique:permissions,name,'.$this->route('permission')->id,
            'description' => 'nullable|string|max:255',
        ];
    }
}
