<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCursusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cursusId = $this->route('cursus')?->id;

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('cursuses', 'code')->ignore($cursusId)],
            'label' => 'required|string|max:150',
            'duration_years' => 'required|integer|min:1|max:8',
        ];
    }
}
