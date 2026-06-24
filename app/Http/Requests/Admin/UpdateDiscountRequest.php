<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
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
           'description' => "nullable|max:120|min:2",
            'category_id' => 'nullable|min:1|exists:categories,id',
            'product_id' => 'nulla|min:1|exists:products,id',
            'starts_at' => 'nullable|date',
            'ends_at'    => 'nullable|date|after:starts_at',
            'value' => 'required|integer',
            'is_active' => "nullable|boolean",
    ];
}

public function withValidator($validator): void
{
    $validator->after(function ($validator) {
        if (!$this->product_id && !$this->category_id) {
            $validator->errors()->add('product_id', 'حداقل یکی از محصول یا دسته‌بندی الزامی است.');
        }
    });
}
}