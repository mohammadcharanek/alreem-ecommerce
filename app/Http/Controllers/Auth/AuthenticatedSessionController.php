<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->mergeSessionCartIntoDatabaseCart();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Move the guest session cart into the logged-in user's active database cart.
     *
     * Guests use session('cart').
     * Logged-in users/admins use carts/cart_items.
     *
     * Without this merge, the cart badge can look wrong immediately after login
     * because the navbar starts reading from the database instead of the session.
     */
    protected function mergeSessionCartIntoDatabaseCart(): void
    {
        $sessionCart = session()->get('cart', []);

        if (!Auth::check() || empty($sessionCart) || !is_array($sessionCart)) {
            return;
        }

        $cart = Cart::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'status' => 'active',
            ],
            [
                'total_amount' => 0,
            ]
        );

        $products = Product::whereIn('id', array_keys($sessionCart))
            ->get()
            ->keyBy('id');

        foreach ($sessionCart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (!$product) {
                continue;
            }

            $stock = max(0, (int) ($product->stock ?? 0));

            if ($stock <= 0) {
                continue;
            }

            $quantity = max(1, (int) $quantity);

            $item = $cart->items()->firstOrNew([
                'product_id' => $product->id,
            ]);

            $currentQuantity = (int) ($item->quantity ?? 0);

            $item->quantity = min($currentQuantity + $quantity, $stock);
            $item->price = $this->priceFor($product);
            $item->save();
        }

        $this->refreshCartTotal($cart);

        session()->forget('cart');
    }

    protected function priceFor(Product $product): float
    {
        return (float) (
            ($product->discount_price !== null && $product->discount_price > 0)
                ? $product->discount_price
                : $product->price
        );
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
