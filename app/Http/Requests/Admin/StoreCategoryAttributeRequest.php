<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => "required|max:120|min:2",
            'unit' => "required|max:500|min:5",
            'type' => "required|numeric|in:0,1",
            'category_id' => "required|min:1|exists:categories,id",
            'status' => "required|numeric|in:0,1",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'نام الزامی است.',
            'name.max' => 'نام نباید بیشتر از 120 کاراکتر باشد.',
            'name.min' => 'نام باید حداقل 2 کاراکتر داشته باشد.',

            'unit.required' => 'واحد الزامی است.',
            'unit.max' => 'واحد نباید بیشتر از 500 کاراکتر باشد.',
            'unit.min' => 'واحد باید حداقل 5 کاراکتر داشته باشد.',

            'type.required' => 'نوع الزامی است.',
            'type.numeric' => 'نوع باید یک عدد باشد.',
            'type.in' => 'نوع باید 0 یا 1 باشد.',

            'category_id.required' => 'دسته‌بندی الزامی است.',
            'category_id.min' => 'دسته‌بندی باید حداقل 1 باشد.',
            'category_id.exists' => 'دسته‌بندی انتخابی معتبر نمی‌باشد.',

            'status.required' => 'وضعیت الزامی است.',
            'status.numeric' => 'وضعیت باید یک عدد باشد.',
            'status.in' => 'وضعیت باید 0 یا 1 باشد.',
        ];
    }
}
