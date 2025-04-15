<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'contact' => 'nullable|min:2|max:255',
            'image' => 'nullable|max:3000|image|mimes:png,jpg,jpeg,gif',
            'price' => 'nullable|numeric',
            'tags' => 'nullable',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'willing_to_trade' => 'nullable|numeric|in:0,1',
        ];
    }
}
