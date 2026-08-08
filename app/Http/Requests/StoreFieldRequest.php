<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cursus_id' => 'required|exists:cursuses,id',
            'code' => 'required|string|max:20',
            'label' => 'required|string|max:150',
        ];
    }
}
