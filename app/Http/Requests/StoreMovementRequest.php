<?php

namespace App\Http\Requests;

use App\Models\Movement;

class StoreMovementRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:'.Movement::ENTRADA.','.Movement::SALIDA,
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ];
    }
}
