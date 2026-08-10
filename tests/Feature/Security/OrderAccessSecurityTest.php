<?php

namespace Tests\Feature\Security;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderAccessSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('status');
            $table->string('payment_method')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('voucher_code')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function test_owner_can_view_their_order(): void
    {
        [$owner, $order] = $this->makeOrder();

        $this->actingAs($owner)
            ->get(route('checkout.thankyou', $order))
            ->assertOk()
            ->assertSee('Private Product')
            ->assertSee('PRIVATE-VOUCHER');
    }

    public function test_another_customer_cannot_view_the_order(): void
    {
        [, $order] = $this->makeOrder();
        $otherCustomer = $this->makeUser('other@example.test');

        $this->actingAs($otherCustomer)
            ->get(route('checkout.thankyou', $order))
            ->assertForbidden()
            ->assertDontSee('PRIVATE-VOUCHER');
    }

    public function test_guest_cannot_view_private_order_data(): void
    {
        [, $order] = $this->makeOrder();

        $this->get(route('checkout.thankyou', $order))
            ->assertRedirect(route('login'))
            ->assertDontSee('PRIVATE-VOUCHER');
    }

    public function test_signed_links_do_not_bypass_authentication_or_ownership(): void
    {
        [, $order] = $this->makeOrder();

        $validSignedUrl = URL::signedRoute('checkout.thankyou', $order);
        $expiredUrl = URL::temporarySignedRoute(
            'checkout.thankyou',
            now()->subMinute(),
            $order
        );

        $this->get($validSignedUrl)->assertRedirect(route('login'));
        $this->get($expiredUrl)->assertRedirect(route('login'));

        $otherCustomer = $this->makeUser('signed-other@example.test');

        $this->actingAs($otherCustomer)
            ->get($validSignedUrl)
            ->assertForbidden();
    }

    public function test_admin_can_view_customer_order(): void
    {
        [, $order] = $this->makeOrder();
        $admin = $this->makeUser('admin@example.test', true);

        $this->actingAs($admin)
            ->get(route('checkout.thankyou', $order))
            ->assertOk()
            ->assertSee('PRIVATE-VOUCHER');
    }

    private function makeOrder(): array
    {
        $owner = $this->makeUser('owner@example.test');

        $productId = DB::table('products')->insertGetId([
            'name' => 'Private Product',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $owner->id,
            'total_amount' => 25,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'shipping_address' => 'Private address',
            'voucher_code' => 'PRIVATE-VOUCHER',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => 1,
            'price' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$owner, Order::query()->findOrFail($orderId)];
    }

    private function makeUser(string $email, bool $isAdmin = false): User
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password',
        ]);

        $user->forceFill([
            'is_admin' => $isAdmin,
        ])->save();

        return $user;
    }
}
