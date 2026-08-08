<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field_id' => 'required|exists:fields,id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'code' => 'required|string|max:10',
            'label' => 'required|string|max:100',
            'order' => 'required|integer|min:1|max:20',
        ];
    }
}
