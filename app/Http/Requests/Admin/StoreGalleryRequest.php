<?php

namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;


class StoreGalleryRequest extends FormRequest
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
            'product_id' => 'required|min:1|exists:products,id',
            'images' => 'required|array',
            'images.*' => 'required|max:3000|image|mimes:png,jpg,jpeg,gif',
            "status" => "nullable|numeric|in:0,1",
        ];
    }
    public function messages(): array
    {
        return [
            'product_id.required' => 'شناسه محصول الزامی است.',
            'product_id.min' => 'شناسه محصول باید حداقل ۱ باشد.',
            'product_id.exists' => 'محصول انتخاب‌شده وجود ندارد.',

            'images.required' => 'بارگذاری حداقل یک تصویر الزامی است.',
            'images.array' => 'فیلد تصاویر باید به صورت آرایه باشد.',

            'images.*.required' => 'همه تصاویر الزامی هستند.',
            'images.*.max' => 'حجم هر تصویر نباید بیشتر از ۳ مگابایت باشد.',
            'images.*.image' => 'هر فایل باید یک تصویر معتبر باشد.',
            'images.*.mimes' => 'فرمت تصاویر باید png، jpg، jpeg یا gif باشد.',

            'status.numeric' => 'وضعیت باید یک عدد باشد.',
            'status.in' => 'وضعیت فقط می‌تواند ۰ (غیرفعال) یا ۱ (فعال) باشد.',
        ];
    }
}
