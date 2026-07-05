<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryAttributeRequest extends FormRequest
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
            'unit' => "nullable|max:500",
            'type' => "required|numeric|in:0,1,3",
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

            'unit.max' => 'واحد نباید بیشتر از 500 کاراکتر باشد.',

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