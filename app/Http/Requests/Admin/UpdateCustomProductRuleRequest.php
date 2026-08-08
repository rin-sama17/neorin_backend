<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomProductRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id'                 => 'nullable|integer',
            'parent_rule_id'           => 'nullable|exists:custom_product_rules,id',

            'condition_type'           => ['sometimes', 'required', Rule::in([
                'selected_value', 'selected_fabric', 'fabric_width', 'dimension',
                'calculated_result', 'quantity', 'material', 'current_price',
                'previous_selection', 'custom_state',
            ])],
            'condition_source'         => 'nullable|string|max:255',
            'condition_operator'       => ['sometimes', 'required', Rule::in([
                'equals', 'not_equals', 'contains', 'greater_than', 'less_than',
                'in', 'not_in', 'between', 'is_empty', 'is_not_empty',
            ])],
            'condition_value'          => 'nullable|string|max:255',
            'condition_reference_id'   => 'nullable|integer',

            'action'                   => ['sometimes', 'required', Rule::in([
                'show', 'hide', 'enable', 'disable', 'require', 'optional',
                'set_value', 'clear_value', 'limit_options', 'load_options',
                'change_formula', 'change_price', 'change_gallery',
                'show_message', 'show_warning', 'show_information',
                'run_calculation', 'refresh_summary',
            ])],

            'target_type'              => ['sometimes', 'required', Rule::in([
                'attribute', 'category_value', 'fabric', 'gallery', 'message',
                'price', 'formula', 'dimension', 'production_option',
                'dynamic_field', 'validation', 'calculation', 'item',
            ])],
            'target_id'                => 'nullable|integer',

            'payload'                  => 'nullable|array',
            'priority'                 => 'nullable|integer|min:0',
            'status'                   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'condition_type.required'     => 'نوع شرط الزامی است.',
            'condition_type.in'           => 'نوع شرط معتبر نیست.',
            'condition_operator.required' => 'عملگر شرط الزامی است.',
            'condition_operator.in'       => 'عملگر شرط معتبر نیست.',
            'action.required'             => 'عملیات الزامی است.',
            'action.in'                   => 'عملیات معتبر نیست.',
            'target_type.required'        => 'نوع هدف الزامی است.',
            'target_type.in'              => 'نوع هدف معتبر نیست.',
            'payload.array'               => 'بار باید آرایه باشد.',
        ];
    }
}
