<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomProductItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                    => 'required|string|max:120|min:2',
            'category_id'             => 'required|exists:categories,id',
            'calculation_profile_id'  => 'nullable|exists:calculation_profiles,id',
            'is_required'             => 'nullable|boolean',
            'min_qty'                 => 'nullable|integer|min:1',
            'max_qty'                 => 'nullable|integer|min:1|gte:min_qty',
            'sort'                    => 'nullable|integer|min:0',
            'status'                  => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'نام آیتم الزامی است.',
            'category_id.required' => 'دسته‌بندی الزامی است.',
            'category_id.exists'   => 'دسته‌بندی انتخابی معتبر نیست.',
            'min_qty.integer'      => 'حداقل تعداد باید عددی باشد.',
            'max_qty.integer'      => 'حداکثر تعداد باید عددی باشد.',
            'max_qty.gte'          => 'حداکثر تعداد نمی‌تواند کمتر از حداقل باشد.',
        ];
    }
}
