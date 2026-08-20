<?php

namespace Tests\Feature\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_uses_oauth_state_protection(): void
    {
        config()->set('services.google', [
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'redirect' => 'http://localhost/auth/google/callback',
        ]);

        $response = $this->get(route('google.redirect'));

        $response->assertRedirect();

        $location = (string) $response->headers->get('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        $this->assertStringStartsWith(
            'https://accounts.google.com/o/oauth2/auth',
            $location
        );
        $this->assertNotEmpty($query['state'] ?? null);
        $response->assertSessionHas('state', $query['state']);
    }

    public function test_new_user_can_sign_up_with_google(): void
    {
        $this->mockGoogleUser($this->googleUser());

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);

        $user = User::firstOrFail();

        $this->assertSame('Google Customer', $user->name);
        $this->assertSame('customer@example.com', $user->email);
        $this->assertSame('google-123', $user->google_id);
        $this->assertSame('https://example.com/avatar.jpg', $user->avatar);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_existing_google_user_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'google_id' => 'google-123',
        ]);

        $this->mockGoogleUser($this->googleUser());

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_existing_password_user_is_linked_by_verified_google_email(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'customer@example.com',
            'google_id' => null,
        ]);

        $this->mockGoogleUser($this->googleUser());

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('google-123', $user->fresh()->google_id);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_unverified_google_email_is_not_trusted_for_account_linking(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'google_id' => null,
        ]);

        $this->mockGoogleUser($this->googleUser([
            'email_verified' => false,
        ]));

        $response = $this->from(route('login'))
            ->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_callback_failure_redirects_safely(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new RuntimeException('OAuth request failed'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->from(route('login'))
            ->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('google');
        $this->assertGuest();
    }

    public function test_repeated_google_callback_does_not_duplicate_user(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->twice()
            ->andReturn($this->googleUser());

        Socialite::shouldReceive('driver')
            ->twice()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('google.callback'));
        Auth::logout();
        $this->get(route('google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'google_id' => 'google-123',
        ]);
    }

    public function test_guest_session_cart_survives_google_login(): void
    {
        $product = Product::create([
            'name' => 'Google Cart Product',
            'price' => 25,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->mockGoogleUser($this->googleUser());

        $response = $this->withSession([
            'cart' => [$product->id => 2],
        ])->get(route('google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionMissing('cart');

        $cart = Cart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 25,
        ]);
        $this->assertSame('50.00', $cart->fresh()->total_amount);
    }

    private function mockGoogleUser(SocialiteUser $googleUser): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function googleUser(array $overrides = []): SocialiteUser
    {
        return SocialiteUser::fake(array_merge([
            'id' => 'google-123',
            'name' => 'Google Customer',
            'nickname' => null,
            'email' => 'customer@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
            'email_verified' => true,
        ], $overrides));
    }
}
