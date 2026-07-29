<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

            return redirect()->intended('/');
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
}