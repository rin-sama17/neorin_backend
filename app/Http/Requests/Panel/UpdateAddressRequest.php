<?php

namespace App\Http\Requests\Panel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'state_id'    => ['required', 'exists:states,id'],
            'city_id'     => ['required', 'exists:cities,id'],
            'address'     => ['required', 'string', 'max:500'],
            'plaque'      => ['required', 'string', 'max:20'],
            'unit'        => ['nullable', 'string', 'max:20'],
            'postal_code' => ['required', 'digits:10'],
            'title'       => ['nullable', 'string', 'max:50'],
            'is_default'  => ['sometimes', 'boolean'],
        ];
    }
}
