<?php

namespace App\Http\Requests;

class UpdateUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,'.$this->route('user')->id,
            'password' => 'sometimes|nullable|string|min:8',
            'role_id' => 'sometimes|required|exists:roles,id',
        ];
    }
}
