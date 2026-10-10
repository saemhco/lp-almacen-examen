<?php

namespace App\Http\Requests;

use App\Models\Movement;

class UpdateMovementRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'item_id' => 'sometimes|required|exists:items,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'type' => 'sometimes|required|in:'.Movement::ENTRADA.','.Movement::SALIDA,
            'quantity' => 'sometimes|required|integer|min:1',
            'note' => 'nullable|string',
        ];
    }
}
