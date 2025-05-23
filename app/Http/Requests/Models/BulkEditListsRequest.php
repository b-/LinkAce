<?php

namespace App\Http\Requests\Models;

use App\Rules\ModelVisibility;
use Illuminate\Foundation\Http\FormRequest;

class BulkEditListsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'models' => ['required', 'string'],
            'visibility' => ['nullable', new ModelVisibility],
        ];
    }
}
