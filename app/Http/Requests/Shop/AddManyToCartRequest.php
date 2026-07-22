<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class AddManyToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                                 => 'required|array|min:1',
            'items.*.item_type'                     => 'required|in:product,custom_product',
            'items.*.product_id'                    => 'required_if:items.*.item_type,product|exists:products,id',
            'items.*.size_id'                       => 'nullable|exists:sizes,id',
            'items.*.quantity'                      => 'required|integer|min:1|max:99',
            'items.*.fabric_ids'                    => 'required|array',
            'items.*.fabric_ids.*'                  => 'exists:fabrics,id',
            'items.*.attributes'                    => 'nullable|array',
            'items.*.attributes.*.attribute_id'     => 'required|exists:category_attributes,id',
            'items.*.attributes.*.value_id'         => 'nullable|exists:category_values,id',
            'items.*.attributes.*.value'            => 'nullable|string',
        ];
    }
}
