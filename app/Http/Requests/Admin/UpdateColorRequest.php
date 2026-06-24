<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return [
            'name'        => 'required|string|max:120|min:2',
            'hex'         => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'نام رنگ الزامی است.',
            'name.max'           => 'نام رنگ نباید بیشتر از 120 کاراکتر باشد.',
            'name.min'           => 'نام رنگ باید حداقل 2 کاراکتر داشته باشد.',

            'hex.required'       => 'کد رنگ الزامی است.',
            'hex.regex'          => 'کد رنگ باید فرمت معتبر هگز باشد. مثال: #FF0000',
 
        ];
    }
}
