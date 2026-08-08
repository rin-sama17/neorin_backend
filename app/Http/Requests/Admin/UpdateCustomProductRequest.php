<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|required|string|max:120|min:2',
            'description' => 'nullable|string|max:500',
            'status'      => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام محصول اختصاصی الزامی است.',
            'name.max'      => 'نام نباید بیشتر از ۱۲۰ کاراکتر باشد.',
            'name.min'      => 'نام باید حداقل ۲ کاراکتر داشته باشد.',
        ];
    }
}
