<?php

namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;


class StoreProductsRequest extends FormRequest
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
            'title' => 'required|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., ]+$/u',
            'description' => 'nullable|min:2|max:700',
            'product_type' => 'nullable|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء_-ي., ]+$/u',
            'product_status' => 'required|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء_-ي., ]+$/u',
            'category_id' => 'required|min:1|exists:categories,id',
            'city_id' => 'required|min:1|exists:cities,id',
            "status" => "nullable|numeric|in:0,1",
            'published_at' => 'nullable|date',
            'expierd_at' => 'nullable|date',
            'contact' => 'nullable|min:2|max:255',
            'is_special' => "nullable|numeric|in:0,1",
            'is_ladder' => "nullable|numeric|in:0,1",
            'image' => 'nullable|max:3000|image|mimes:png,jpg,jpeg,gif',
            'price' => 'nullable|regex:/^[,0-9\-۰-۹]+$/u',
            'tags' => 'nullable',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'willing_to_trade' => 'nullable|numeric|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان محصول الزامی است.',
            'title.max' => 'عنوان محصول نباید بیشتر از 120 کاراکتر باشد.',
            'title.min' => 'عنوان محصول باید حداقل 2 کاراکتر داشته باشد.',
            'title.regex' => 'عنوان محصول باید شامل حروف فارسی، انگلیسی، اعداد و برخی علائم خاص باشد.',

            'description.min' => 'توضیحات باید حداقل 2 کاراکتر داشته باشد.',
            'description.max' => 'توضیحات نباید بیشتر از 700 کاراکتر باشد.',

            'product_type.required' => 'نوع محصول الزامی است.',
            'product_type.max' => 'نوع محصول نباید بیشتر از 120 کاراکتر باشد.',
            'product_type.min' => 'نوع محصول باید حداقل 2 کاراکتر داشته باشد.',
            'product_type.regex' => 'نوع محصول باید شامل حروف فارسی، انگلیسی، اعداد و برخی علائم خاص باشد.',

            'product_status.required' => 'وضعیت محصول الزامی است.',
            'product_status.max' => 'وضعیت محصول نباید بیشتر از 120 کاراکتر باشد.',
            'product_status.min' => 'وضعیت محصول باید حداقل 2 کاراکتر داشته باشد.',
            'product_status.regex' => 'وضعیت محصول باید شامل حروف فارسی، انگلیسی، اعداد و برخی علائم خاص باشد.',

            'category_id.required' => 'دسته‌بندی محصول الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخابی معتبر نمی‌باشد.',

            'city_id.required' => 'شهر الزامی است.',
            'city_id.exists' => 'شهر انتخابی معتبر نمی‌باشد.',

            'status.numeric' => 'وضعیت باید یک عدد باشد.',
            'status.in' => 'وضعیت باید 0 یا 1 باشد.',

            'published_at.date' => 'تاریخ انتشار باید یک تاریخ معتبر باشد.',
            'expierd_at.date' => 'تاریخ انقضاء باید یک تاریخ معتبر باشد.',

            'contact.min' => 'اطلاعات تماس باید حداقل 2 کاراکتر داشته باشد.',
            'contact.max' => 'اطلاعات تماس نباید بیشتر از 255 کاراکتر باشد.',

            'image.max' => 'حجم تصویر نباید بیشتر از 3000 کیلوبایت باشد.',
            'image.image' => 'فایل انتخابی باید یک تصویر باشد.',
            'image.mimes' => 'فرمت تصویر باید یکی از فرمت‌های png، jpg، jpeg، gif باشد.',

            'willing_to_trade.numeric' => 'وضعیت مایل به معامله باید یک عدد باشد.',
            'willing_to_trade.in' => 'وضعیت مایل به معامله باید 0 یا 1 باشد.',
        ];
    }
}
