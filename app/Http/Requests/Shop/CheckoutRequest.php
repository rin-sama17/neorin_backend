<?php

namespace App\Http\Requests\Shop;


use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_method'       => 'required|in:online,cod,wallet',
            'notes'                => 'nullable|string|max:500',
            'address'              => 'required|array',
            'address.full_name'    => 'required|string|max:120',
            'address.phone'        => 'required|string|max:20',
            'address.province'     => 'required|string|max:100',
            'address.city'         => 'required|string|max:100',
            'address.postal_code'  => 'required|string|max:20',
            'address.address_line' => 'required|string|max:500',
        ];
    }
}
