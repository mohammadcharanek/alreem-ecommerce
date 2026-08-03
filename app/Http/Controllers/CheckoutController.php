<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseConfirmation;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\TwilioWhatsAppService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Return the product's effective unit price.
     */
    protected function unitPrice(Product $product): float
    {
        return round((float) $product->display_price, 2);
    }

    /**
     * Move items from the session cart into the user's active database cart.
     */
    protected function migrateSessionCartToDbCart(int $userId): Cart
    {
        $sessionCart = session()->get('cart', []);

        /** @var Cart $cart */
        $cart = Cart::firstOrCreate(
            [
                'user_id' => $userId,
                'status' => 'active',
            ],
            [
                'total_amount' => 0,
            ]
        );

        if (empty($sessionCart)) {
            return $cart->load('items.product.images');
        }

        $productIds = array_map(
            'intval',
            array_keys($sessionCart)
        );

        $products = Product::query()
            ->whereIn('id', $productIds, 'and', false)
            ->get([
                'id',
                'price',
                'discount_price',
                'stock',
                'is_active',
            ])
            ->keyBy('id');

        foreach ($sessionCart as $productId => $requestedQuantity) {
            $productId = (int) $productId;
            $requestedQuantity = max(0, (int) $requestedQuantity);

            if ($requestedQuantity < 1) {
                continue;
            }

            /** @var Product|null $product */
            $product = $products->get($productId);

            if (
                ! $product
                || ! (bool) $product->is_active
                || (int) $product->stock < 1
            ) {
                continue;
            }

            /** @var CartItem $cartItem */
            $cartItem = $cart->items()->firstOrNew([
                'product_id' => $productId,
            ]);

            $existingQuantity = (int) ($cartItem->quantity ?? 0);

            $cartItem->quantity = min(
                $existingQuantity + $requestedQuantity,
                (int) $product->stock
            );
            $cartItem->price = $this->unitPrice($product);

            $cartItem->save();
        }

        /*
         * Clear the session cart after migration so refreshing the checkout
         * page does not add the same session quantities repeatedly.
         */
        session()->forget('cart');

        return $cart->fresh([
            'items.product.images',
        ]);
    }

    /**
     * Display the checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return redirect()
                ->route('login')
                ->with('error', 'Please log in before checking out.');
        }

        $cart = $this->migrateSessionCartToDbCart($userId);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $computed = $cart->items->map(
            function (CartItem $item): array {
                $product = $item->product;
                $quantity = max(1, (int) $item->quantity);

                if (! $product) {
                    return [
                        'item' => $item,
                        'product' => null,
                        'qty' => $quantity,
                        'unit' => 0.0,
                        'line' => 0.0,
                    ];
                }

                $unitPrice = $this->unitPrice($product);

                return [
                    'item' => $item,
                    'product' => $product,
                    'qty' => $quantity,
                    'unit' => $unitPrice,
                    'line' => round($unitPrice * $quantity, 2),
                ];
            }
        );

        $subtotal = round((float) $computed->sum('line'), 2);

        return view('checkout.index', [
            'cart' => $cart,
            'computed' => $computed,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Process checkout and create an order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => [
                'required',
                'string',
                'max:30',
            ],
            'shipping_address' => [
                'required',
                'string',
                'max:1000',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:cash_on_delivery,whish_money,omt',
            ],
        ]);

        $user = Auth::user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'Please log in before checking out.');
        }

        $normalizedPhone = $this->normalizeLebanesePhone(
            $validated['phone']
        );

        if (
            ! $normalizedPhone
            || ! preg_match('/^\+961\d{7,8}$/', $normalizedPhone)
        ) {
            return back()
                ->withInput($request->except('_token'))
                ->withErrors([
                    'phone' => 'Enter a valid Lebanese phone number.',
                ]);
        }

        $user->update([
            'phone' => $normalizedPhone,
        ]);

        /*
         * Refresh the authenticated model so the normalized phone number is
         * available when notifications are generated.
         */
        $user->refresh();

        $userId = (int) $user->id;

        /** @var Cart|null $cart */
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->with('items')
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            $cart = $this->migrateSessionCartToDbCart($userId);
        }

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        try {
            /** @var Order $order */
            $order = DB::transaction(
                function () use (
                    $cart,
                    $userId,
                    $validated
                ): Order {
                    /*
                     * Lock the cart row to reduce the possibility of the same
                     * cart being checked out twice simultaneously.
                     */
                    /** @var Cart|null $lockedCart */
                    $lockedCart = Cart::query()
                        ->whereKey($cart->id)
                        ->where('user_id', $userId)
                        ->where('status', 'active')
                        ->lockForUpdate()
                        ->first();

                    if (! $lockedCart) {
                        throw new DomainException(
                            'This cart has already been processed.'
                        );
                    }

                    $cartItems = CartItem::query()
                        ->where('cart_id', $lockedCart->id)
                        ->lockForUpdate()
                        ->get();

                    if ($cartItems->isEmpty()) {
                        throw new DomainException(
                            'Your cart is empty.'
                        );
                    }

                    $productIds = $cartItems
                        ->pluck('product_id')
                        ->map(fn ($id) => (int) $id)
                        ->values();

                    $products = Product::query()
                        ->whereIn(
                            'id',
                            $productIds->all(),
                            'and',
                            false
                        )
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    $total = 0.0;
                    $orderLines = [];
                    $stockErrors = [];

                    foreach ($cartItems as $cartItem) {
                        /** @var Product|null $product */
                        $product = $products->get(
                            (int) $cartItem->product_id
                        );

                        $quantity = (int) $cartItem->quantity;

                        if (! $product) {
                            $stockErrors[] = sprintf(
                                'Unknown product (#%d)',
                                (int) $cartItem->product_id
                            );

                            continue;
                        }

                        if ($quantity < 1) {
                            $stockErrors[] = $product->name;

                            continue;
                        }

                        if ((int) $product->stock < $quantity) {
                            $stockErrors[] = $product->name;

                            continue;
                        }

                        $unitPrice = $this->unitPrice($product);
                        $lineTotal = round(
                            $unitPrice * $quantity,
                            2
                        );

                        $total += $lineTotal;

                        $orderLines[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                        ];

                        $cartItem->update([
                            'price' => $unitPrice,
                        ]);
                    }

                    if (! empty($stockErrors)) {
                        throw new DomainException(
                            'Sorry, not enough stock for: '
                            . implode(', ', array_unique($stockErrors))
                        );
                    }

                    if (empty($orderLines)) {
                        throw new DomainException(
                            'No valid products were found in your cart.'
                        );
                    }

                    /** @var Order $order */
                    $order = Order::create([
                        'user_id' => $userId,
                        'total_amount' => round($total, 2),
                        'status' => 'pending',
                        'payment_method' => $validated['payment_method'],
                        'shipping_address' => trim(
                            $validated['shipping_address']
                        ),
                        'voucher_code' => $this->generateUniqueVoucherCode(),
                    ]);

                    foreach ($orderLines as $line) {
                        /** @var Product $product */
                        $product = $line['product'];
                        $quantity = (int) $line['quantity'];
                        $unitPrice = (float) $line['unit_price'];

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'price' => $unitPrice,
                        ]);

                        /*
                         * Because the product row is locked and stock was
                         * checked above, this update remains consistent.
                         */
                        $product->decrement('stock', $quantity, []);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'quantity' => -$quantity,
                            'movement_type' => 'sale',
                            'reference_id' => $order->id,
                            'description' => sprintf(
                                'Stock reduced for Order #%d',
                                $order->id
                            ),
                        ]);
                    }

                    /*
                     * Delete cart items before marking the cart as ordered.
                     */
                    CartItem::query()
                        ->where('cart_id', $lockedCart->id)
                        ->delete();

                    $lockedCart->update([
                        'status' => 'ordered',
                        'total_amount' => round($total, 2),
                    ]);

                    return $order;
                },
                3
            );

            session()->forget('cart');

            $order->load([
                'user',
                'items.product',
            ]);

            /*
             * Notification failures are caught and logged inside these
             * methods, so a successful order is not cancelled if SMTP or
             * Twilio temporarily fails.
             */
            $this->sendOrderEmail($order);
            $this->sendOrderWhatsAppNotifications($order);

            $signedUrl = URL::signedRoute(
                'checkout.thankyou',
                [
                    'oid' => $order->id,
                ]
            );

            return redirect()->to($signedUrl);
        } catch (DomainException $exception) {
            Log::warning('Order could not be completed.', [
                'user_id' => $userId,
                'cart_id' => $cart->id ?? null,
                'reason' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->except('_token'))
                ->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Order processing failed.', [
                'user_id' => $userId,
                'cart_id' => $cart->id ?? null,
                'error' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            return back()
                ->withInput($request->except('_token'))
                ->with(
                    'error',
                    'We could not complete your order. Please try again.'
                );
        }
    }

    /**
     * Generate an order voucher code that does not already exist.
     */
    private function generateUniqueVoucherCode(): string
    {
        do {
            $voucherCode = Str::upper(
                Str::random(8)
            );
        } while (
            Order::query()
                ->where('voucher_code', $voucherCode)
                ->exists()
        );

        return $voucherCode;
    }

    /**
     * Send order confirmation emails to the customer and administrator.
     */
    private function sendOrderEmail(Order $order): void
    {
        $order->loadMissing([
            'user',
            'items.product',
        ]);

        $customerEmail = $order->user?->email;

        $adminEmail = config(
            'mail.admin_address'
        );

        if (! $customerEmail) {
            Log::warning(
                'Customer order email skipped because the email is missing.',
                [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]
            );
        } else {
            try {
                Mail::to($customerEmail)->send(
                    new PurchaseConfirmation($order)
                );

                Log::info(
                    'Customer order confirmation email sent.',
                    [
                        'order_id' => $order->id,
                        'email' => $customerEmail,
                    ]
                );
            } catch (Throwable $exception) {
                Log::error(
                    'Customer order confirmation email failed.',
                    [
                        'order_id' => $order->id,
                        'email' => $customerEmail,
                        'error' => $exception->getMessage(),
                    ]
                );
            }
        }

        if (! $adminEmail) {
            Log::warning(
                'Admin order email skipped because MAIL_ADMIN_ADDRESS is missing.',
                [
                    'order_id' => $order->id,
                ]
            );

            return;
        }

        if (
            $customerEmail
            && strcasecmp($customerEmail, $adminEmail) === 0
        ) {
            Log::info(
                'Admin order email skipped because the customer and admin addresses match.',
                [
                    'order_id' => $order->id,
                    'email' => $adminEmail,
                ]
            );

            return;
        }

        try {
            Mail::to($adminEmail)->send(
                new PurchaseConfirmation($order, true)
            );

            Log::info(
                'Admin order notification email sent.',
                [
                    'order_id' => $order->id,
                    'email' => $adminEmail,
                ]
            );
        } catch (Throwable $exception) {
            Log::error(
                'Admin order notification email failed.',
                [
                    'order_id' => $order->id,
                    'email' => $adminEmail,
                    'error' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * Send WhatsApp order notifications through TwilioWhatsAppService.
     */
    private function sendOrderWhatsAppNotifications(
        Order $order
    ): void {
        $sendAdmin = (bool) config(
            'services.twilio.send_admin_whatsapp',
            true
        );

        $sendCustomer = (bool) config(
            'services.twilio.send_customer_whatsapp',
            false
        );

        if (! $sendAdmin && ! $sendCustomer) {
            Log::info('WhatsApp order notifications are disabled.', [
                'order_id' => $order->id,
            ]);

            return;
        }

        $order->loadMissing([
            'user',
            'items.product',
        ]);

        try {
            /** @var TwilioWhatsAppService $whatsapp */
            $whatsapp = app(TwilioWhatsAppService::class);
        } catch (Throwable $exception) {
            Log::error(
                'Twilio WhatsApp service could not be created.',
                [
                    'order_id' => $order->id,
                    'error' => $exception->getMessage(),
                ]
            );

            return;
        }

        $adminPhone = $this->normalizeLebanesePhone(
            (string) config('services.twilio.admin_whatsapp')
        );

        $customerPhone = $this->normalizeLebanesePhone(
            $order->user?->phone
        );

        $productList = $order->items
            ->map(function (OrderItem $item): string {
                $productName = $item->product?->name
                    ?? 'Product #' . $item->product_id;

                return sprintf(
                    '- %s x%d @ $%s',
                    $productName,
                    (int) $item->quantity,
                    number_format((float) $item->price, 2)
                );
            })
            ->implode("\n");

        $customerName = $order->user?->name ?: 'Customer';
        $formattedTotal = number_format(
            (float) $order->total_amount,
            2
        );
        $paymentMethod = $this->formatPaymentMethod(
            (string) $order->payment_method
        );

        $adminMessage = implode("\n", [
            'New order received on Al Reem Expo',
            '',
            "Order: #{$order->id}",
            "Customer: {$customerName}",
            'Phone: ' . ($customerPhone ?: 'Not provided'),
            "Payment: {$paymentMethod}",
            'Status: ' . ucfirst((string) $order->status),
            "Total: \${$formattedTotal}",
            'Voucher: ' . ($order->voucher_code ?: 'N/A'),
            '',
            'Shipping address:',
            (string) $order->shipping_address,
            '',
            'Products:',
            $productList ?: 'No products found.',
        ]);

        $customerMessage = implode("\n", [
            "Hello {$customerName},",
            '',
            'Thank you for ordering from Al Reem Expo.',
            "Your order #{$order->id} was received successfully.",
            "Total: \${$formattedTotal}",
            "Payment: {$paymentMethod}",
            'Status: Pending confirmation',
            '',
            'We will contact you to confirm delivery.',
        ]);

        if ($sendAdmin) {
            if (! $adminPhone) {
                Log::warning(
                    'Admin WhatsApp notification skipped because the number is missing.',
                    ['order_id' => $order->id]
                );
            } else {
                $this->sendWhatsAppSafely(
                    $whatsapp,
                    $adminPhone,
                    $adminMessage,
                    'admin',
                    $order
                );
            }
        }

        if (! $sendCustomer) {
            Log::info('Customer WhatsApp notification is disabled.', [
                'order_id' => $order->id,
            ]);

            return;
        }

        if (! $customerPhone) {
            Log::warning(
                'Customer WhatsApp notification skipped because the phone number is missing.',
                [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]
            );

            return;
        }

        if (
            $sendAdmin
            && $adminPhone
            && hash_equals($adminPhone, $customerPhone)
        ) {
            Log::info(
                'Customer WhatsApp notification skipped because it matches the admin number.',
                [
                    'order_id' => $order->id,
                    'recipient' => $this->maskPhone($customerPhone),
                ]
            );

            return;
        }

        $this->sendWhatsAppSafely(
            $whatsapp,
            $customerPhone,
            $customerMessage,
            'customer',
            $order
        );
    }

    private function sendWhatsAppSafely(
        TwilioWhatsAppService $whatsapp,
        string $recipient,
        string $message,
        string $recipientType,
        Order $order
    ): void {
        try {
            $sent = $whatsapp->send($recipient, $message);

            if ($sent) {
                Log::info(
                    ucfirst($recipientType)
                    . ' WhatsApp order notification submitted.',
                    [
                        'order_id' => $order->id,
                        'recipient' => $this->maskPhone($recipient),
                    ]
                );

                return;
            }

            Log::warning(
                ucfirst($recipientType)
                . ' WhatsApp service returned false.',
                [
                    'order_id' => $order->id,
                    'recipient' => $this->maskPhone($recipient),
                ]
            );
        } catch (Throwable $exception) {
            Log::error(
                ucfirst($recipientType)
                . ' WhatsApp order notification failed.',
                [
                    'order_id' => $order->id,
                    'recipient' => $this->maskPhone($recipient),
                    'error' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * Normalize Lebanese phone numbers into E.164 format.
     *
     * Examples:
     * 03 123 456   -> +9613123456
     * 70123456     -> +96170123456
     * 009613123456 -> +9613123456
     * +9613123456  -> +9613123456
     */
    private function normalizeLebanesePhone(
        ?string $phone
    ): ?string {
        if (! $phone) {
            return null;
        }

        $phone = trim($phone);

        /*
         * Remove an accidentally supplied Twilio channel prefix.
         */
        $phone = preg_replace(
            '/^whatsapp:/i',
            '',
            $phone
        ) ?? $phone;

        /*
         * Remove spaces, dashes, parentheses and other non-phone symbols,
         * while retaining a possible leading plus sign.
         */
        $phone = preg_replace(
            '/[^\d+]/',
            '',
            $phone
        ) ?? $phone;

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '00961')) {
            return '+961' . substr($phone, 5);
        }

        if (str_starts_with($phone, '961')) {
            return '+' . $phone;
        }

        if (str_starts_with($phone, '+961')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '+961' . substr($phone, 1);
        }

        if (! str_starts_with($phone, '+')) {
            return '+961' . $phone;
        }

        return $phone;
    }

    /**
     * Convert the database payment identifier into readable text.
     */
    private function formatPaymentMethod(
        string $paymentMethod
    ): string {
        return match ($paymentMethod) {
            'cash_on_delivery' => 'Cash on delivery',
            'whish_money' => 'Whish Money',
            'omt' => 'OMT',
            default => Str::headline($paymentMethod),
        };
    }

    /**
     * Hide most phone digits in Laravel logs.
     */
    private function maskPhone(string $phone): string
    {
        $phone = trim($phone);

        if (strlen($phone) <= 4) {
            return '****';
        }

        return str_repeat(
            '*',
            max(0, strlen($phone) - 4)
        ) . substr($phone, -4);
    }

    /**
     * Display the order thank-you page.
     */
    public function thankYou(
        Request $request
    ): View|RedirectResponse {
        $orderId = $request->route('oid')
            ?? session('order_id');

        if (! $orderId) {
            return redirect()
                ->route('home')
                ->with('error', 'No order found.');
        }

        /** @var Order|null $order */
        $order = Order::query()
            ->with([
                'user',
                'items.product',
            ])
            ->find($orderId);

        if (! $order) {
            return redirect()
                ->route('home')
                ->with('error', 'Order not found.');
        }

        $isSigned = $request->hasValidSignature();

        $isOwner = Auth::check()
            && (int) Auth::id() === (int) $order->user_id;

        if (! $isSigned && ! $isOwner) {
            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Unauthorized access to this order.'
                );
        }

        return view(
            'checkout.thankyou',
            compact('order')
        );
    }
}