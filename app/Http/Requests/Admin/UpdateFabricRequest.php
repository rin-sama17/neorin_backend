<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFabricRequest extends FormRequest
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
            'material' => "required|max:120|min:2",
            'color' => "required|max:120|min:2",
            'image' => 'nullable|max:3000|image|mimes:png,jpg,jpeg,gif',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_id' => 'required|min:1|exists:categories,id',
            'price' => 'required|regex:/^[,0-9\-۰-۹]+$/u',
            'status' => "required|numeric|in:0,1",
        ];
        
    }
}
