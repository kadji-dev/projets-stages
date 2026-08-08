<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaptopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $laptopId = $this->route('laptop')?->id;

        return [
            'reference' => ['required', 'string', 'max:30', Rule::unique('laptops', 'reference')->ignore($laptopId)],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'serial_number' => ['nullable', 'string', 'max:100', Rule::unique('laptops', 'serial_number')->ignore($laptopId)],
            'status' => 'required|in:disponible,attribue,maintenance',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
