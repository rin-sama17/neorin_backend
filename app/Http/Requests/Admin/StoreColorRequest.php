<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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