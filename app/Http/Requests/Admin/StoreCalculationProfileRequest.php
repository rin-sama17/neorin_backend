<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalculationProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:120|min:2',
            'description'    => 'nullable|string|max:500',
            'strategy_type'  => ['required', Rule::in([
                'quilt', 'fitted_sheet', 'pillow', 'mattress_cover', 'blanket',
            ])],
            'config'         => 'nullable|array',
            'profit_percent' => 'nullable|numeric|min:0|max:100',
            'vat_percent'    => 'nullable|numeric|min:0|max:100',
            'is_active'      => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'نام پروفایل الزامی است.',
            'strategy_type.required' => 'نوع استراتژی الزامی است.',
            'strategy_type.in'    => 'نوع استراتژی معتبر نیست.',
        ];
    }
}
