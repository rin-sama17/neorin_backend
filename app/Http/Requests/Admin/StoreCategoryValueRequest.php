<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryValueRequest extends FormRequest
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
            'value' => "required|max:120|min:2",
            'type' => "required|numeric|in:0,1",
            'category_attribute_id' => "required|min:1|exists:category_attributes,id",
            'status' => "required|numeric|in:0,1",
        ];
    }
    public function messages(): array
    {
        return [
            'value.required' => 'وارد کردن مقدار الزامی است.',
            'value.max' => 'مقدار نباید بیشتر از ۱۲۰ کاراکتر باشد.',
            'value.min' => 'مقدار باید حداقل ۲ کاراکتر باشد.',

            'type.required' => 'نوع الزامی است.',
            'type.numeric' => 'نوع باید عددی باشد.',
            'type.in' => 'نوع فقط می‌تواند ۰ یا ۱ باشد.',

            'category_attribute_id.required' => 'شناسه ویژگی دسته‌بندی الزامی است.',
            'category_attribute_id.min' => 'شناسه ویژگی دسته‌بندی باید حداقل ۱ باشد.',
            'category_attribute_id.exists' => 'ویژگی دسته‌بندی انتخاب‌شده وجود ندارد.',

            'status.required' => 'وضعیت الزامی است.',
            'status.numeric' => 'وضعیت باید یک عدد باشد.',
            'status.in' => 'وضعیت فقط می‌تواند ۰ (غیرفعال) یا ۱ (فعال) باشد.',
        ];
    }
}
