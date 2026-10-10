<?php

namespace App\Http\Requests;

class StoreLocationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:locations,code',
        ];
    }
}
