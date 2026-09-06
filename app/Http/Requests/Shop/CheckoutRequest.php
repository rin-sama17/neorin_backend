<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'exists:addresses,id'],

            'new_address'              => ['required_without:address_id', 'array'],
            'new_address.state_id'     => ['required_without:address_id', 'exists:states,id'],
            'new_address.city_id'      => ['required_without:address_id', 'exists:cities,id'],
            'new_address.address'      => ['required_without:address_id', 'string', 'max:500'],
            'new_address.plaque'       => ['required_without:address_id', 'string', 'max:20'],
            'new_address.unit'         => ['nullable', 'string', 'max:20'],
            'new_address.postal_code'  => ['required_without:address_id', 'digits:10'],
            'new_address.title'        => ['nullable', 'string', 'max:50'],
            'new_address.is_default'   => ['sometimes', 'boolean'],

            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('address_id')) {
                $owns = $this->user()
                    ->addresses()
                    ->whereKey($this->address_id)
                    ->exists();

                if (! $owns) {
                    $validator->errors()->add('address_id', 'این آدرس متعلق به شما نیست.');
                }
            }
        });
    }
}
