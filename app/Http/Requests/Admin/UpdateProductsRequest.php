<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // اصلی
            'title'            => 'required|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., ]+$/u',
            'description'      => 'nullable|min:2|max:700',
            'material'         => 'nullable|min:2|max:255',
            'category_id'      => 'required|exists:categories,id',
            'city_id'          => 'nullable|exists:cities,id',
            'status'           => 'nullable|in:0,1,2',
            'is_special'       => 'nullable|boolean',
            'price'            => 'nullable|numeric|min:0',
            'stock'            => 'nullable|integer|min:0',
            'slug'             => 'nullable|string|unique:products,slug',
            'tags'             => 'nullable|string',

            // عکس
            'image'            => 'nullable|image|mimes:png,jpg,jpeg,gif|max:3000',

            // SEO
            'meta_title'       => 'nullable|max:120',
            'meta_description' => 'nullable|max:300',

            // روابط
            'fabric_ids'       => 'nullable|array',
            'fabric_ids.*'     => 'exists:fabrics,id',
            'color_ids'        => 'nullable|array',
            'color_ids.*'      => 'exists:colors,id',
            
            // سایزها
            'sizes'            => 'nullable|array',
            'sizes.*.id'       => 'required|exists:size,id',
            'sizes.*.price'    => 'nullable|numeric|min:0',
            'sizes.*.stock'    => 'nullable|integer|min:0',
            'sizes.*.image'    => 'nullable|image|mimes:png,jpg,jpeg,gif|max:3000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'عنوان محصول الزامی است.',
            'title.max'            => 'عنوان محصول نباید بیشتر از 120 کاراکتر باشد.',
            'title.min'            => 'عنوان محصول باید حداقل 2 کاراکتر داشته باشد.',
            'title.regex'          => 'عنوان محصول فقط شامل حروف فارسی، انگلیسی و اعداد باشد.',
            'category_id.required' => 'دسته‌بندی الزامی است.',
            'category_id.exists'   => 'دسته‌بندی معتبر نیست.',
            'city_id.exists'       => 'شهر معتبر نیست.',
            'image.max'            => 'حجم تصویر نباید بیشتر از 3MB باشد.',
            'image.mimes'          => 'فرمت تصویر باید png، jpg، jpeg یا gif باشد.',
            'fabric_ids.*.exists'  => 'یکی از پارچه‌ها معتبر نیست.',
            'color_ids.*.exists'   => 'یکی از رنگ‌ها معتبر نیست.',
            'sizes.*.id.exists'    => 'یکی از سایزها معتبر نیست.',
        ];
    }
}