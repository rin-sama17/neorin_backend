<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_type'                         => 'required|in:product,custom_product',
            'product_id'                        => 'required_if:item_type,product|exists:products,id',
            'size_id'                           => 'nullable|exists:sizes,id',
            'quantity'                          => 'required|integer|min:1|max:99',
            'fabric_ids'                        => 'required_if:item_type,custom_product|array',
            'fabric_ids.*'                      => 'exists:fabrics,id',
            'custom_product_id'                 => 'required_if:item_type,custom_product|exists:custom_products,id',
            'custom_product_item_id'            => 'required_if:item_type,custom_product|exists:custom_product_items,id',
            'dimensions'                        => 'required_if:item_type,custom_product|array',
            'dimensions.width'                  => 'required_with:dimensions|numeric|min:1',
            'dimensions.height'                 => 'required_with:dimensions|numeric|min:1',
            'dimensions.depth'                  => 'nullable|numeric|min:0',
            'attributes'                        => 'nullable|array',
            'attributes.*.attribute_id'         => 'required|exists:category_attributes,id',
            'attributes.*.value_id'             => 'nullable|exists:category_values,id',
            'attributes.*.value'                => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'item_type.required'                     => 'نوع آیتم الزامی است.',
            'item_type.in'                           => 'نوع آیتم معتبر نیست.',
            'product_id.required_if'                 => 'شناسه محصول الزامی است.',
            'product_id.exists'                      => 'محصول انتخابی معتبر نیست.',
            'size_id.exists'                         => 'سایز انتخابی معتبر نیست.',
            'quantity.required'                      => 'تعداد الزامی است.',
            'quantity.min'                           => 'تعداد نمی‌تواند کمتر از ۱ باشد.',
            'quantity.max'                           => 'تعداد نمی‌تواند بیشتر از ۹۹ باشد.',
            'fabric_ids.required_if'                 => 'انتخاب پارچه برای محصول اختصاصی الزامی است.',
            'fabric_ids.*.exists'                    => 'پارچه انتخابی معتبر نیست.',
            'custom_product_id.required_if'          => 'محصول اختصاصی الزامی است.',
            'custom_product_id.exists'               => 'محصول اختصاصی انتخابی معتبر نیست.',
            'custom_product_item_id.required_if'     => 'آیتم محصول اختصاصی الزامی است.',
            'custom_product_item_id.exists'          => 'آیتم محصول اختصاصی انتخابی معتبر نیست.',
            'dimensions.required_if'                 => 'ابعاد برای محصول اختصاصی الزامی است.',
            'dimensions.width.required_with'         => 'عرض الزامی است.',
            'dimensions.width.numeric'               => 'عرض باید عددی باشد.',
            'dimensions.height.required_with'        => 'ارتفاع الزامی است.',
            'dimensions.height.numeric'              => 'ارتفاع باید عددی باشد.',
            'dimensions.depth.numeric'               => 'عمق باید عددی باشد.',
            'attributes.*.attribute_id.required'     => 'شناسه ویژگی الزامی است.',
            'attributes.*.attribute_id.exists'       => 'ویژگی انتخابی معتبر نیست.',
            'attributes.*.value_id.exists'           => 'مقدار ویژگی انتخابی معتبر نیست.',
        ];
    }
}
