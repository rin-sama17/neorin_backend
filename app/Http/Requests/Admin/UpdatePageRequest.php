<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'title' => "required|max:120|min:2",
            'body' => "required|max:1000000|min:1",
            'status' => "required|numeric|in:0,1",
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الزامی است.',
            'title.max' => 'عنوان نباید بیشتر از ۱۲۰ کاراکتر باشد.',
            'title.min' => 'عنوان باید حداقل ۲ کاراکتر باشد.',

            'body.required' => 'بدنه الزامی است.',
            'body.max' => 'بدنه نباید بیشتر از ۱,۰۰۰,۰۰۰ کاراکتر باشد.',
            'body.min' => 'بدنه باید حداقل ۱ کاراکتر داشته باشد.',

            'status.required' => 'وضعیت الزامی است.',
            'status.numeric' => 'وضعیت باید یک عدد باشد.',
            'status.in' => 'وضعیت فقط می‌تواند ۰ (غیرفعال) یا ۱ (فعال) باشد.',
        ];
    }
}
