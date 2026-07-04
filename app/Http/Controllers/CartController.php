<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /* ----------------------------- Helpers ----------------------------- */

    protected function priceFor(Product $product): float
    {
        return (float) (
            ($product->discount_price !== null && $product->discount_price > 0)
                ? $product->discount_price
                : $product->price
        );
    }

    protected function activeCart(): Cart
    {
        return Cart::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'status' => 'active',
            ],
            [
                'total_amount' => 0,
            ]
        );
    }

    /**
     * Build a simple map: [product_id => quantity].
     * Authenticated users/admins use database cart.
     * Guests use session cart.
     */
    protected function getCartMap(): array
    {
        if (Auth::check()) {
            $cart = $this->activeCart();

            return $cart->items()
                ->pluck('quantity', 'product_id')
                ->map(fn ($quantity) => (int) $quantity)
                ->toArray();
        }

        $cart = session()->get('cart', []);

        return is_array($cart) ? $cart : [];
    }

    protected function setSessionMap(array $map): void
    {
        session()->put('cart', $map);
    }

    protected function cartCountFromMap(array $map): int
    {
        return (int) array_sum(array_map('intval', $map));
    }

    protected function cartSubtotalFromMap(array $map): float
    {
        if (empty($map)) {
            return 0.0;
        }

        $products = Product::whereIn('id', array_keys($map))
            ->get(['id', 'price', 'discount_price']);

        $priceById = $products->mapWithKeys(fn ($product) => [
            $product->id => $this->priceFor($product),
        ]);

        $sum = 0.0;

        foreach ($map as $productId => $quantity) {
            $sum += ($priceById[$productId] ?? 0.0) * (int) $quantity;
        }

        return round($sum, 2);
    }

    protected function refreshCartTotal(Cart $cart): void
    {
        $total = (float) $cart->items()
            ->get()
            ->sum(function ($item) {
                return ((float) $item->price) * ((int) $item->quantity);
            });

        $cart->forceFill([
            'total_amount' => round($total, 2),
        ])->save();
    }

    protected function jsonCartOk(string $message, array $map): JsonResponse
    {
        return response()->json([
            'success' => $message,
            'cart' => [
                'count' => $this->cartCountFromMap($map),
                'subtotal' => $this->cartSubtotalFromMap($map),
            ],
        ]);
    }

    protected function jsonCartError(string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], $status);
    }

    /* -------------------------------- Pages -------------------------------- */

    public function index()
    {
        if (Auth::check()) {
            $cart = $this->activeCart();
            $cart->load('items.product.images');

            $map = $cart->items
                ->pluck('quantity', 'product_id')
                ->map(fn ($quantity) => (int) $quantity)
                ->toArray();

            $products = $cart->items
                ->pluck('product')
                ->filter()
                ->values();

            return view('cart.index', [
                'products' => $products,
                'cart' => $map,
            ]);
        }

        $map = session()->get('cart', []);
        $map = is_array($map) ? $map : [];

        $products = empty($map)
            ? collect()
            : Product::with('images')->whereIn('id', array_keys($map))->get();

        return view('cart.index', [
            'products' => $products,
            'cart' => $map,
        ]);
    }

    /* ------------------------------- Actions ------------------------------- */

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);

        $qty = (int) ($validated['quantity'] ?? 1);
        $stock = (int) ($product->stock ?? 0);

        if ($stock <= 0) {
            return $request->expectsJson()
                ? $this->jsonCartError('This product is out of stock.')
                : back()->with('error', 'This product is out of stock.');
        }

        if (Auth::check()) {
            $cart = $this->activeCart();

            /** @var CartItem $item */
            $item = $cart->items()->firstOrNew([
                'product_id' => $product->id,
            ]);

            $current = (int) ($item->quantity ?? 0);

            $item->quantity = min($current + $qty, $stock);
            $item->price = $this->priceFor($product);
            $item->save();

            $this->refreshCartTotal($cart);

            $map = $this->getCartMap();

            return $request->expectsJson()
                ? $this->jsonCartOk('Product added to cart!', $map)
                : back()->with('success', 'Product added to cart!');
        }

        $map = session()->get('cart', []);
        $map = is_array($map) ? $map : [];

        $map[$product->id] = min((int) ($map[$product->id] ?? 0) + $qty, $stock);

        $this->setSessionMap($map);

        return $request->expectsJson()
            ? $this->jsonCartOk('Product added to cart!', $map)
            : back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        $qty = (int) $validated['quantity'];
        $stock = (int) ($product->stock ?? 0);

        if (Auth::check()) {
            $cart = $this->activeCart();

            /** @var CartItem|null $item */
            $item = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            if ($qty === 0) {
                if ($item) {
                    $item->delete();
                }

                $this->refreshCartTotal($cart);

                $map = $this->getCartMap();

                return $request->expectsJson()
                    ? $this->jsonCartOk('Item removed from cart.', $map)
                    : back()->with('success', 'Item removed from cart.');
            }

            if ($stock < $qty) {
                return $request->expectsJson()
                    ? $this->jsonCartError('Requested quantity exceeds stock.')
                    : back()->with('error', 'Requested quantity exceeds stock.');
            }

            if (!$item) {
                $item = new CartItem([
                    'product_id' => $product->id,
                ]);

                $item->cart_id = $cart->id;
            }

            $item->quantity = $qty;
            $item->price = $this->priceFor($product);
            $item->save();

            $this->refreshCartTotal($cart);

            $map = $this->getCartMap();

            return $request->expectsJson()
                ? $this->jsonCartOk('Quantity updated.', $map)
                : back()->with('success', 'Quantity updated.');
        }

        $map = session()->get('cart', []);
        $map = is_array($map) ? $map : [];

        if ($qty === 0) {
            unset($map[$product->id]);

            $this->setSessionMap($map);

            return $request->expectsJson()
                ? $this->jsonCartOk('Item removed from cart.', $map)
                : back()->with('success', 'Item removed from cart.');
        }

        if ($stock < $qty) {
            return $request->expectsJson()
                ? $this->jsonCartError('Requested quantity exceeds stock.')
                : back()->with('error', 'Requested quantity exceeds stock.');
        }

        $map[$product->id] = $qty;

        $this->setSessionMap($map);

        return $request->expectsJson()
            ? $this->jsonCartOk('Quantity updated.', $map)
            : back()->with('success', 'Quantity updated.');
    }

    public function remove(Request $request, Product $product)
    {
        if (Auth::check()) {
            $cart = $this->activeCart();

            $cart->items()
                ->where('product_id', $product->id)
                ->delete();

            $this->refreshCartTotal($cart);

            $map = $this->getCartMap();

            return $request->expectsJson()
                ? $this->jsonCartOk('Item removed from cart.', $map)
                : back()->with('success', 'Item removed from cart.');
        }

        $map = session()->get('cart', []);
        $map = is_array($map) ? $map : [];

        unset($map[$product->id]);

        $this->setSessionMap($map);

        return $request->expectsJson()
            ? $this->jsonCartOk('Item removed from cart.', $map)
            : back()->with('success', 'Item removed from cart.');
    }

    public function clear(Request $request)
    {
        if (Auth::check()) {
            $cart = $this->activeCart();

            $cart->items()->delete();

            $cart->forceFill([
                'total_amount' => 0,
            ])->save();

            $map = [];
        } else {
            session()->forget('cart');

            $map = [];
        }

        return $request->expectsJson()
            ? response()->json([
                'success' => 'Cart cleared.',
                'cart' => [
                    'count' => 0,
                    'subtotal' => 0.0,
                ],
            ])
            : back()->with('success', 'Cart cleared.');
    }
}
