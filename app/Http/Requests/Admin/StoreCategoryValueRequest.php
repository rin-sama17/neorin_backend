<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryValueRequest extends FormRequest
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
           'value'=> "required|max:120|min:2",
           'type'=>"required|numeric|in:0,1",
           'category_attribute_id'=>"required|min:1|exists:category_attributes,id",
           'status'=>"required|numeric|in:0,1",
        ];
    }
}
