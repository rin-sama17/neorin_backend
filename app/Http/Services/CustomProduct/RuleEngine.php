<?php

namespace App\Http\Services\CustomProduct;

use App\Models\Product\CustomProduct;
use App\Models\Product\CustomProductItem;
use App\Models\Product\CustomProductRule;

class RuleEngine
{
    public function evaluate(CustomProductItem $item, array $allSelections): array
    {
        $result = [
            'visible_attributes'  => [],
            'hidden_attributes'   => [],
            'required_attributes' => [],
            'optional_attributes' => [],
            'disabled_attributes' => [],
            'values'              => [],
            'messages'            => [],
            'warnings'            => [],
            'payloads'            => [],
            'actions'             => [],
        ];

        $allAttributes = $item->getAttributesConfig();

        foreach ($allAttributes as $attribute) {
            $result['visible_attributes'][] = $attribute['id'];
            if ($attribute['is_required']) {
                $result['required_attributes'][] = $attribute['id'];
            }
        }

        $rules = $item->rules()
            ->topLevel()
            ->active()
            ->with('childRules')
            ->orderBy('priority')
            ->get();

        $itemSelections = $allSelections[$item->id] ?? $allSelections;

        foreach ($rules as $rule) {
            $this->evaluateRule($rule, $itemSelections, $allSelections, $result);
        }

        $result['visible_attributes'] = array_values(array_unique($result['visible_attributes']));
        $result['hidden_attributes']  = array_values(array_unique($result['hidden_attributes']));
        $result['required_attributes'] = array_values(array_unique($result['required_attributes']));
        $result['optional_attributes'] = array_values(array_unique($result['optional_attributes']));
        $result['disabled_attributes'] = array_values(array_unique($result['disabled_attributes']));
        $result['visible_attributes'] = array_values(array_diff(
            $result['visible_attributes'],
            $result['hidden_attributes']
        ));

        return $result;
    }

    public function evaluateCrossItem(CustomProduct $customProduct, array $selections): array
    {
        $results = [];

        $items = $customProduct->items()->where('status', 1)->orderBy('sort')->get();

        foreach ($items as $item) {
            $results[$item->id] = $this->evaluate($item, $selections);
        }

        $crossItemRules = CustomProductRule::whereHas('customProductItem', function ($q) use ($customProduct) {
            $q->where('custom_product_id', $customProduct->id);
        })
            ->where('target_type', 'item')
            ->topLevel()
            ->active()
            ->orderBy('priority')
            ->get();

        foreach ($crossItemRules as $rule) {
            $sourceItemId = $rule->custom_product_item_id;
            $sourceSelections = $selections[$sourceItemId] ?? [];

            $conditionMet = $this->evaluateCondition($rule, $sourceSelections, $selections);

            if ($conditionMet) {
                $targetItemId = $rule->target_id;
                if (isset($results[$targetItemId])) {
                    $this->applyAction($rule, $results[$targetItemId], $rule->payload ?? []);
                }
            }
        }

        foreach ($items as $item) {
            $results[$item->id]['visible_attributes'] = array_values(array_unique($results[$item->id]['visible_attributes']));
            $results[$item->id]['hidden_attributes']  = array_values(array_unique($results[$item->id]['hidden_attributes']));
            $results[$item->id]['visible_attributes'] = array_values(array_diff(
                $results[$item->id]['visible_attributes'],
                $results[$item->id]['hidden_attributes']
            ));
        }

        return $results;
    }

    private function evaluateRule(CustomProductRule $rule, array $itemSelections, array $allSelections, array &$result): void
    {
        $conditionMet = $this->evaluateCondition($rule, $itemSelections, $allSelections);

        if ($conditionMet) {
            $this->applyAction($rule, $result, $rule->payload ?? []);

            if ($rule->childRules->count() > 0) {
                foreach ($rule->childRules->where('status', true) as $childRule) {
                    $this->evaluateRule($childRule, $itemSelections, $allSelections, $result);
                }
            }
        }
    }

    private function evaluateCondition(CustomProductRule $rule, array $itemSelections, array $allSelections): bool
    {
        $currentValue = $this->resolveConditionValue($rule, $itemSelections, $allSelections);

        $ruleValue = $rule->condition_value;
        $operator  = $rule->condition_operator;

        if (is_array($currentValue)) {
            $currentValue = $currentValue['value_id'] ?? $currentValue['value'] ?? null;
        }

        return match ($operator) {
            'equals'       => $this->compareValues($currentValue, $ruleValue, 'equals'),
            'not_equals'   => $this->compareValues($currentValue, $ruleValue, 'not_equals'),
            'contains'     => str_contains((string) $currentValue, (string) $ruleValue),
            'greater_than' => (float) $currentValue > (float) $ruleValue,
            'less_than'    => (float) $currentValue < (float) $ruleValue,
            'in'           => in_array($currentValue, explode(',', (string) $ruleValue)),
            'not_in'       => !in_array($currentValue, explode(',', (string) $ruleValue)),
            'between'      => $this->evaluateBetween($currentValue, $ruleValue),
            'is_empty'     => empty($currentValue),
            'is_not_empty' => !empty($currentValue),
            default        => false,
        };
    }

    private function resolveConditionValue(CustomProductRule $rule, array $itemSelections, array $allSelections): mixed
    {
        $source  = $rule->condition_source;
        $type    = $rule->condition_type;
        $refId   = $rule->condition_reference_id;

        return match ($type) {
            'selected_value' => $this->resolveSelectedValue($source, $itemSelections),
            'selected_fabric' => $this->resolveSelectedFabric($source, $itemSelections),
            'fabric_width'   => $this->resolveFabricWidth($source, $itemSelections),
            'dimension'      => $this->resolveDimension($source, $itemSelections),
            'quantity'       => $itemSelections['quantity'] ?? null,
            'custom_state'   => $itemSelections[$source] ?? null,
            'previous_selection' => $itemSelections[$source] ?? null,
            default          => $itemSelections[$source] ?? null,
        };
    }

    private function resolveSelectedValue(?string $source, array $selections): mixed
    {
        if (!$source) {
            return null;
        }

        $value = $selections[$source] ?? null;

        if (is_array($value)) {
            return $value['value_id'] ?? $value['value'] ?? null;
        }

        return $value;
    }

    private function resolveSelectedFabric(?string $source, array $selections): mixed
    {
        if (!$source) {
            return $selections['fabric_id'] ?? null;
        }

        return $selections[$source] ?? null;
    }

    private function resolveFabricWidth(?string $source, array $selections): mixed
    {
        $fabricId = $selections[$source ?? 'fabric_id'] ?? null;

        if (!$fabricId) {
            return null;
        }

        $fabric = \App\Models\Product\Fabric::find($fabricId);

        return $fabric?->width;
    }

    private function resolveDimension(?string $source, array $selections): mixed
    {
        $dimensions = $selections['dimensions'] ?? [];

        if (!$source) {
            return null;
        }

        return $dimensions[$source] ?? null;
    }

    private function compareValues(mixed $current, mixed $rule, string $operator): bool
    {
        if ($current === null && $rule === null) {
            return $operator === 'equals';
        }

        if ($current === null || $rule === null) {
            return $operator === 'not_equals';
        }

        return match ($operator) {
            'equals'     => (string) $current === (string) $rule,
            'not_equals' => (string) $current !== (string) $rule,
            default      => false,
        };
    }

    private function evaluateBetween(mixed $current, ?string $ruleValue): bool
    {
        if (!$ruleValue) {
            return false;
        }

        $parts = array_map('trim', explode(',', $ruleValue));

        if (count($parts) !== 2) {
            return false;
        }

        $min = (float) $parts[0];
        $max = (float) $parts[1];
        $val = (float) $current;

        return $val >= $min && $val <= $max;
    }

    private function applyAction(CustomProductRule $rule, array &$result, array $payload): void
    {
        $targetId = $rule->target_id;
        $action   = $rule->action;

        switch ($action) {
            case 'show':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['visible_attributes'][] = $targetId;
                    $result['hidden_attributes'] = array_values(array_diff($result['hidden_attributes'], [$targetId]));
                }
                break;

            case 'hide':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['hidden_attributes'][] = $targetId;
                    $result['visible_attributes'] = array_values(array_diff($result['visible_attributes'], [$targetId]));
                }
                break;

            case 'enable':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['disabled_attributes'] = array_values(array_diff($result['disabled_attributes'], [$targetId]));
                }
                break;

            case 'disable':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['disabled_attributes'][] = $targetId;
                }
                break;

            case 'require':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['required_attributes'][] = $targetId;
                    $result['optional_attributes'] = array_values(array_diff($result['optional_attributes'], [$targetId]));
                }
                break;

            case 'optional':
                if ($rule->target_type === 'attribute' && $targetId) {
                    $result['optional_attributes'][] = $targetId;
                    $result['required_attributes'] = array_values(array_diff($result['required_attributes'], [$targetId]));
                }
                break;

            case 'set_value':
                if ($targetId) {
                    $result['values'][$targetId] = $payload['value'] ?? $rule->condition_value;
                }
                break;

            case 'clear_value':
                if ($targetId) {
                    unset($result['values'][$targetId]);
                }
                break;

            case 'show_message':
                $result['messages'][] = [
                    'type'    => 'information',
                    'message' => $payload['message'] ?? '',
                    'target'  => $targetId,
                ];
                break;

            case 'show_warning':
                $result['warnings'][] = [
                    'type'    => 'warning',
                    'message' => $payload['message'] ?? '',
                    'target'  => $targetId,
                ];
                break;

            case 'show_information':
                $result['messages'][] = [
                    'type'    => 'info',
                    'message' => $payload['message'] ?? '',
                    'target'  => $targetId,
                ];
                break;

            default:
                $result['actions'][] = [
                    'action'     => $action,
                    'target_type' => $rule->target_type,
                    'target_id'   => $targetId,
                    'payload'     => $payload,
                ];
                break;
        }

        if (!empty($payload)) {
            $result['payloads'][] = [
                'action'     => $action,
                'target_type' => $rule->target_type,
                'target_id'   => $targetId,
                'payload'     => $payload,
            ];
        }
    }

    public function getVisibleAttributes(CustomProductItem $item, array $selections): array
    {
        $evaluation = $this->evaluate($item, $selections);
        $allAttributes = $item->getAttributesConfig();

        return $allAttributes->filter(function ($attr) use ($evaluation) {
            return in_array($attr['id'], $evaluation['visible_attributes']);
        })->values()->all();
    }

    public function getRequiredAttributeIds(CustomProductItem $item, array $selections): array
    {
        $evaluation = $this->evaluate($item, $selections);
        return $evaluation['required_attributes'];
    }
}
