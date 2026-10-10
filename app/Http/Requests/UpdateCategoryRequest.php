<?php

namespace App\Http\Requests;

class UpdateCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,'.$this->route('category')->id,
            'description' => 'nullable|string',
        ];
    }
}
