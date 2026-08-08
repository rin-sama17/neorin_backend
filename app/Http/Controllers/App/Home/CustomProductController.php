<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\CustomProductResource;
use App\Http\Services\CustomProduct\RuleEngine;
use App\Models\Product\CustomProduct;
use App\Models\Product\CustomProductItem;
use App\Models\Product\Fabric;
use App\Models\Shop\CartItemCategoryValue;
use App\Services\Order\Calculation\StrategyResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomProductController extends Controller
{
    public function __construct(
        private StrategyResolver $strategyResolver,
        private RuleEngine $ruleEngine,
    ) {}

    public function index(): JsonResponse
    {
        $products = CustomProduct::with([
            'items' => fn($q) => $q->where('status', 1)->orderBy('sort'),
            'items.category',
            'items.calculationProfile',
        ])->where('status', 1)->get();

        return response()->json(
            CustomProductResource::collection($products)
        );
    }

    public function show(string $slug): JsonResponse
    {
        $product = CustomProduct::with([
            'items' => fn($q) => $q->where('status', 1)->orderBy('sort'),
            'items.category',
            'items.calculationProfile',
            'items.rules',
        ])->where('slug', $slug)
          ->where('status', 1)
          ->firstOrFail();

        return response()->json(
            new CustomProductResource($product)
        );
    }

    public function showItem(int $itemId): JsonResponse
    {
        $item = CustomProductItem::with([
            'category',
            'calculationProfile',
            'rules.childRules',
            'customProduct',
        ])->where('id', $itemId)->firstOrFail();

        $rules = $item->rules()
            ->topLevel()
            ->active()
            ->orderBy('priority')
            ->get();

        return response()->json([
            'id'                   => $item->id,
            'name'                 => $item->name,
            'is_required'          => $item->is_required,
            'min_qty'              => $item->min_qty,
            'max_qty'              => $item->max_qty,
            'category'             => [
                'id'   => $item->category->id,
                'name' => $item->category->name,
            ],
            'calculation_profile'  => $item->calculationProfile ? [
                'id'            => $item->calculationProfile->id,
                'name'          => $item->calculationProfile->name,
                'strategy_type' => $item->calculationProfile->strategy_type,
            ] : null,
            'attributes'           => $item->getAttributesConfig(),
            'fabrics'              => $item->getFabrics()->map(fn($f) => [
                'id'       => $f->id,
                'title'    => $f->title,
                'slug'     => $f->slug,
                'material' => $f->material,
                'width'    => $f->width,
                'price'    => $f->price,
                'image'    => $f->image,
                'colors'   => $f->colors,
            ]),
            'rules'                => $rules->map(fn($r) => [
                'id'                     => $r->id,
                'group_id'               => $r->group_id,
                'parent_rule_id'         => $r->parent_rule_id,
                'condition_type'         => $r->condition_type,
                'condition_source'       => $r->condition_source,
                'condition_operator'     => $r->condition_operator,
                'condition_value'        => $r->condition_value,
                'condition_reference_id' => $r->condition_reference_id,
                'action'                 => $r->action,
                'target_type'            => $r->target_type,
                'target_id'              => $r->target_id,
                'payload'                => $r->payload,
                'priority'               => $r->priority,
                'child_rules'            => $r->childRules->map(fn($c) => [
                    'id'                     => $c->id,
                    'group_id'               => $c->group_id,
                    'parent_rule_id'         => $c->parent_rule_id,
                    'condition_type'         => $c->condition_type,
                    'condition_source'       => $c->condition_source,
                    'condition_operator'     => $c->condition_operator,
                    'condition_value'        => $c->condition_value,
                    'condition_reference_id' => $c->condition_reference_id,
                    'action'                 => $c->action,
                    'target_type'            => $c->target_type,
                    'target_id'              => $c->target_id,
                    'payload'                => $c->payload,
                    'priority'               => $c->priority,
                ]),
            ]),
        ]);
    }

    public function evaluateRules(Request $request): JsonResponse
    {
        $request->validate([
            'item_id'    => 'required|exists:custom_product_items,id',
            'selections' => 'required|array',
        ]);

        $item = CustomProductItem::with(['category', 'calculationProfile'])->findOrFail($request->item_id);

        $result = $this->ruleEngine->evaluate($item, $request->selections);

        $visibleAttributes = collect($item->getAttributesConfig())
            ->filter(fn($attr) => in_array($attr['id'], $result['visible_attributes']))
            ->values();

        return response()->json([
            'visible_attributes'   => $result['visible_attributes'],
            'hidden_attributes'    => $result['hidden_attributes'],
            'required_attributes'  => $result['required_attributes'],
            'optional_attributes'  => $result['optional_attributes'],
            'disabled_attributes'  => $result['disabled_attributes'],
            'values'               => $result['values'],
            'messages'             => $result['messages'],
            'warnings'             => $result['warnings'],
            'payloads'             => $result['payloads'],
            'attributes'           => $visibleAttributes,
        ]);
    }

    public function evaluateAllRules(Request $request): JsonResponse
    {
        $request->validate([
            'custom_product_id' => 'required|exists:custom_products,id',
            'selections'         => 'required|array',
        ]);

        $customProduct = CustomProduct::with([
            'items' => fn($q) => $q->where('status', 1),
            'items.category',
        ])->findOrFail($request->custom_product_id);

        $allSelections = $request->selections;
        $results = $this->ruleEngine->evaluateCrossItem($customProduct, $allSelections);

        $response = [];
        foreach ($customProduct->items as $item) {
            if (!isset($results[$item->id])) {
                continue;
            }

            $itemResult = $results[$item->id];
            $visibleAttributes = collect($item->getAttributesConfig())
                ->filter(fn($attr) => in_array($attr['id'], $itemResult['visible_attributes']))
                ->values();

            $response[$item->id] = [
                'visible_attributes'   => $itemResult['visible_attributes'],
                'hidden_attributes'    => $itemResult['hidden_attributes'],
                'required_attributes'  => $itemResult['required_attributes'],
                'optional_attributes'  => $itemResult['optional_attributes'],
                'disabled_attributes'  => $itemResult['disabled_attributes'],
                'values'               => $itemResult['values'],
                'messages'             => $itemResult['messages'],
                'warnings'             => $itemResult['warnings'],
                'payloads'             => $itemResult['payloads'],
                'attributes'           => $visibleAttributes,
            ];
        }

        return response()->json($response);
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'custom_product_item_id' => 'required|exists:custom_product_items,id',
            'fabric_ids'             => 'required|array|min:1',
            'fabric_ids.*'           => 'exists:fabrics,id',
            'dimensions'             => 'required|array',
            'dimensions.width'       => 'required|numeric|min:1',
            'dimensions.height'      => 'required|numeric|min:1',
            'dimensions.depth'       => 'nullable|numeric|min:0',
            'attributes'             => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:category_attributes,id',
            'attributes.*.value_id'     => 'nullable|exists:category_values,id',
            'attributes.*.value'        => 'nullable|string',
            'quantity'               => 'nullable|integer|min:1|max:99',
        ]);

        $productItem = CustomProductItem::with(['category', 'calculationProfile'])->findOrFail($request->custom_product_item_id);

        $fakeItem = new \App\Models\Shop\CartItem([
            'item_type'     => 'custom_product',
            'quantity'      => $request->integer('quantity', 1),
            'configuration' => [
                'custom_product_item_id' => $productItem->id,
                'dimensions'             => $request->input('dimensions'),
            ],
        ]);

        $fakeItem->setRelation(
            'fabrics',
            Fabric::whereIn('id', $request->fabric_ids)->get()
        );

        $attrs = collect($request->input('attributes', []));
        $categoryValues = $attrs->isNotEmpty()
            ? CartItemCategoryValue::query()
                ->with(['attribute', 'categoryValue'])
                ->whereIn('category_attribute_id', $attrs->pluck('attribute_id'))
                ->get()
            : collect();
        $fakeItem->setRelation('categoryValues', $categoryValues);

        $breakdown = $this->strategyResolver->calculateForItem($fakeItem, $productItem);

        return response()->json([
            'item_name'  => $productItem->name,
            'dimensions' => $request->input('dimensions'),
            'quantity'   => $request->integer('quantity', 1),
            'breakdown'  => $breakdown,
            'unit_price' => $breakdown['total'],
            'total'      => $breakdown['total'] * $request->integer('quantity', 1),
        ]);
    }
}
