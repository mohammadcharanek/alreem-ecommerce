<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the customer to Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google's OAuth callback.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleId = $googleUser->getId();
            $email = strtolower(trim((string) $googleUser->getEmail()));

            if (!$googleId || !$email) {
                return redirect()
                    ->route('login')
                    ->withErrors([
                        'google' => 'Google did not provide the required account information.',
                    ]);
            }

            /*
             * First find an account already linked to this Google account.
             */
            $user = User::where('google_id', $googleId)->first();

            /*
             * Otherwise, find an existing account with the same verified
             * Google email address and safely link it.
             */
            if (!$user) {
                $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
            }

            if ($user) {
                /*
                 * Prevent replacing a different Google account already linked
                 * to this customer account.
                 */
                if (
                    filled($user->google_id) &&
                    $user->google_id !== $googleId
                ) {
                    return redirect()
                        ->route('login')
                        ->withErrors([
                            'google' => 'This email is already linked to another Google account.',
                        ]);
                }

                $user->forceFill([
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();
            } else {
                $user = User::create([
                    'name' => $googleUser->getName()
                        ?: $googleUser->getNickname()
                        ?: 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                    'phone' => null,
                ]);
            }

            Auth::login($user, true);

            request()->session()->regenerate();

            /*
             * Guests keep their cart in the session while authenticated
             * customers use the database cart. Merge the guest cart now so
             * products do not disappear after Google authentication.
             */
            $this->mergeSessionCartIntoDatabaseCart();

            /*
             * If Laravel's auth middleware remembered a protected destination
             * such as /checkout, return there. Otherwise use the dashboard.
             */
            return redirect()->intended(
                route('dashboard', absolute: false)
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::warning('Google authentication failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Google login failed. Please try again.',
                ]);
        }
    }

    /**
     * Move the guest session cart into the authenticated user's active
     * database cart.
     */
    protected function mergeSessionCartIntoDatabaseCart(): void
    {
        $sessionCart = session()->get('cart', []);

        if (
            !Auth::check() ||
            empty($sessionCart) ||
            !is_array($sessionCart)
        ) {
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

            $item->quantity = min(
                $currentQuantity + $quantity,
                $stock
            );
            $item->price = $this->priceFor($product);
            $item->save();
        }

        $this->refreshCartTotal($cart);

        /*
         * Clear the session cart only after it has been merged successfully,
         * preventing the same quantities from being added again.
         */
        session()->forget('cart');
    }

    /**
     * Return the product's effective cart price.
     */
    protected function priceFor(Product $product): float
    {
        return (float) (
            ($product->discount_price !== null && $product->discount_price > 0)
                ? $product->discount_price
                : $product->price
        );
    }

    /**
     * Recalculate and persist the database cart total.
     */
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
}
