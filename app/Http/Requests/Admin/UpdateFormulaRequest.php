<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'sometimes|required|string|max:120|min:2',
            'formula_type' => ['sometimes', 'required', Rule::in([
                'fabric_consumption', 'labor', 'fiber', 'accessories', 'production',
            ])],
            'config'       => 'nullable|array',
            'priority'     => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'نام فرمول الزامی است.',
            'formula_type.required' => 'نوع فرمول الزامی است.',
            'formula_type.in'       => 'نوع فرمول معتبر نیست.',
        ];
    }
}
