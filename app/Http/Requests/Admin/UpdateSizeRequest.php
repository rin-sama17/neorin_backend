<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:120|min:2',
            'width'      => 'required|string|max:20',
            'height'     => 'required|string|max:20',
            'product_id' => 'required|exists:products,id',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'nullable|integer|min:0',
            'image'      => 'nullable|image|mimes:png,jpg,jpeg,gif|max:3000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'نام سایز الزامی است.',
            'name.max'            => 'نام سایز نباید بیشتر از 120 کاراکتر باشد.',
            'name.min'            => 'نام سایز باید حداقل 2 کاراکتر داشته باشد.',

            'width.required'      => 'عرض الزامی است.',
            'width.max'           => 'عرض نباید بیشتر از 20 کاراکتر باشد.',

            'height.required'     => 'ارتفاع الزامی است.',
            'height.max'          => 'ارتفاع نباید بیشتر از 20 کاراکتر باشد.',

            'product_id.required' => 'محصول الزامی است.',
            'product_id.exists'   => 'محصول انتخابی معتبر نیست.',

            'price.required'      => 'قیمت الزامی است.',
            'price.numeric'       => 'قیمت باید عدد باشد.',
            'price.min'           => 'قیمت نمی‌تواند منفی باشد.',

            'stock.integer'       => 'موجودی باید عدد صحیح باشد.',
            'stock.min'           => 'موجودی نمی‌تواند منفی باشد.',

            'image.image'         => 'فایل انتخابی باید تصویر باشد.',
            'image.mimes'         => 'فرمت تصویر باید png، jpg، jpeg یا gif باشد.',
            'image.max'           => 'حجم تصویر نباید بیشتر از 3MB باشد.',
        ];
    }
}