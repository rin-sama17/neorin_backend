<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
           'name'=> "required|max:120|min:2",
           'description'=>"required|max:500|min:5",
           'status'=>"required|numeric|in:0,1",
           'icon'=>'nullable|min:2|max:120',
           'parent_id'=>"nullable|min:1|exists:categories,id",

        ];
    }
}
