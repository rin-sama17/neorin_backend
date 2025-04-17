<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStateRequest extends FormRequest
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
            'parent_id' => "nullable|min:1|exists:states,id",
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'وارد کردن نام الزامی است.',
            'name.max' => 'نام نباید بیشتر از ۱۲۰ کاراکتر باشد.',
            'name.min' => 'نام باید حداقل ۲ کاراکتر باشد.',

            'description.required' => 'توضیحات الزامی است.',
            'description.max' => 'توضیحات نباید بیشتر از ۵۰۰ کاراکتر باشد.',
            'description.min' => 'توضیحات باید حداقل ۵ کاراکتر باشد.',

            'status.required' => 'وضعیت الزامی است.',
            'status.numeric' => 'وضعیت باید عددی باشد.',
            'status.in' => 'وضعیت فقط می‌تواند ۰ (غیرفعال) یا ۱ (فعال) باشد.',

            'parent_id.min' => 'شناسه والد باید حداقل ۱ باشد.',
            'parent_id.exists' => 'وضعیت والد انتخاب‌شده یافت نشد.',
        ];
    }
}
