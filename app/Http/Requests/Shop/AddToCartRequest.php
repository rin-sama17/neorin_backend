<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'attributes'                        => 'nullable|array',
            'attributes.*.attribute_id'         => 'required|exists:category_attributes,id',
            'attributes.*.value_id'             => 'nullable|exists:category_values,id',
            'attributes.*.value'                => 'nullable|string',
        ];
    }
}
