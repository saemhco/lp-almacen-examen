<?php

namespace App\Http\Requests;

class UpdateLocationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:255|unique:locations,code,'.$this->route('location')->id,
        ];
    }
}
