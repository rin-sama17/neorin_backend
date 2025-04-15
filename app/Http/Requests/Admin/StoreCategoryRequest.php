<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'description' => "required|max:500|min:5",
            'status' => "required|numeric|in:0,1",
            'icon' => 'nullable|min:2|max:120',
            'parent_id' => "nullable|min:1|exists:categories,id",

        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'نام دسته‌بندی الزامی است.',
            'name.max' => 'نام دسته‌بندی نباید بیشتر از 120 کاراکتر باشد.',
            'name.min' => 'نام دسته‌بندی باید حداقل 2 کاراکتر داشته باشد.',

            'description.required' => 'توضیحات دسته‌بندی الزامی است.',
            'description.max' => 'توضیحات دسته‌بندی نباید بیشتر از 500 کاراکتر باشد.',
            'description.min' => 'توضیحات دسته‌بندی باید حداقل 5 کاراکتر داشته باشد.',

            'status.required' => 'وضعیت دسته‌بندی الزامی است.',
            'status.numeric' => 'وضعیت دسته‌بندی باید یک عدد باشد.',
            'status.in' => 'وضعیت دسته‌بندی باید 0 یا 1 باشد.',

            'icon.min' => 'آیکن دسته‌بندی باید حداقل 2 کاراکتر داشته باشد.',
            'icon.max' => 'آیکن دسته‌بندی نباید بیشتر از 120 کاراکتر باشد.',

            'parent_id.min' => 'آی‌دی والد باید حداقل 1 باشد.',
            'parent_id.exists' => 'دسته‌بندی والد انتخابی معتبر نمی‌باشد.',
        ];
    }
}
