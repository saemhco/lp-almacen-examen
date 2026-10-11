<?php

namespace App\Http\Requests;

class StoreRegistroRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'codigo_universitario' => 'required|string|max:32',
        ];
    }
}
