<?php

// app/Http/Controllers/App/CartController.php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\AddManyToCartRequest;
use App\Http\Requests\Shop\AddToCartRequest;
use App\Http\Requests\Shop\UpdateCartItemRequest;
use App\Http\Services\Cart\CartService;
use App\Models\Shop\CartItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreate($request);
        return response()->json($this->cartService->summary($cart));
    }

    public function add(AddToCartRequest $request)
    {
        $cart = $this->cartService->getOrCreate($request);
        $this->cartService->add($cart, $request->validated());
        return response()->json($this->cartService->summary($cart), 201);
    }
    public function addMany(AddManyToCartRequest $request)
    {
        $cart   = $this->cartService->getOrCreate($request);
        $result = $this->cartService->addMany($cart, $request->validated('items'));

        return response()->json([
            ...$this->cartService->summary($cart),
            'failed' => $result['failed'],
        ], empty($result['failed']) ? 201 : 207);
    }
    public function update(UpdateCartItemRequest $request, CartItem $item)
    {
        $this->authorize('update', $item);
        $this->cartService->updateQuantity($item, $request->validated('quantity'));
        $cart = $this->cartService->getOrCreate($request);
        return response()->json($this->cartService->summary($cart));
    }

    public function remove(Request $request, CartItem $item)
    {
        $this->authorize('delete', $item);
        $this->cartService->remove($item);
        $cart = $this->cartService->getOrCreate($request);
        return response()->json($this->cartService->summary($cart));
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->getOrCreate($request);
        $this->cartService->clear($cart);
        return response()->json(['message' => 'سبد خرید پاک شد']);
    }
}
