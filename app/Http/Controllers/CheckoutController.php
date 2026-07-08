<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseConfirmation;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class CheckoutController extends Controller
{
    /**
     * Return unit price using discount_price when > 0, else price.
     */
    protected function unitPrice(Product $p): float
    {
        return (float) (
            ($p->discount_price !== null && $p->discount_price > 0)
                ? $p->discount_price
                : $p->price
        );
    }

    /**
     * Ensure any session cart items are migrated into the user's active DB cart.
     */
    protected function migrateSessionCartToDbCart(int $userId): ?Cart
    {
        $sessionCart = session()->get('cart', []);

        /** @var Cart $cart */
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            ['total_amount' => 0]
        );

        if (! empty($sessionCart)) {
            $productIds = array_keys($sessionCart);
            $products = Product::whereIn('id', $productIds)->get(['id', 'stock']);
            $stocks = $products->pluck('stock', 'id');

            foreach ($sessionCart as $pid => $qty) {
                $qty = max(0, (int) $qty);

                if ($qty === 0) {
                    continue;
                }

                $max = (int) ($stocks[$pid] ?? 0);

                if ($max <= 0) {
                    continue;
                }

                $qty = min($qty, $max);

                /** @var CartItem $item */
                $item = $cart->items()->firstOrNew(['product_id' => $pid]);
                $item->quantity = (int) $item->quantity + $qty;
                $item->save();
            }
        }

        return $cart->load('items.product.images');
    }

    /**
     * Show checkout page with computed prices/totals.
     */
    public function index()
    {
        $userId = Auth::id();

        $cart = $this->migrateSessionCartToDbCart($userId);

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $computed = $cart->items->map(function (CartItem $item) {
            $p = $item->product;

            if (! $p) {
                return [
                    'item' => $item,
                    'product' => null,
                    'qty' => (int) $item->quantity,
                    'unit' => 0.0,
                    'line' => 0.0,
                ];
            }

            $unit = $this->unitPrice($p);
            $qty = (int) $item->quantity;

            return [
                'item' => $item,
                'product' => $p,
                'qty' => $qty,
                'unit' => $unit,
                'line' => round($unit * $qty, 2),
            ];
        });

        $subtotal = round($computed->sum('line'), 2);

        return view('checkout.index', [
            'cart' => $cart,
            'computed' => $computed,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Process checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:30',
            'shipping_address' => 'required|string|max:1000',
            'payment_method' => 'required|string|in:cash_on_delivery,whish_money,omt',
        ]);
    if ($request->filled('phone') && Auth::user()) {
    Auth::user()->update([
        'phone' => $this->normalizeLebanesePhone($request->phone),
    ]);
}
        $userId = Auth::id();

        /** @var Cart|null $cart */
        $cart = Cart::where('user_id', $userId)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            $cart = $this->migrateSessionCartToDbCart($userId);
        }

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            $total = 0.0;
            $outOfStock = [];

            foreach ($cart->items as $item) {
                /** @var Product|null $p */
                $p = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (! $p) {
                    $outOfStock[] = "Unknown product (#{$item->product_id})";
                    continue;
                }

                $price = $this->unitPrice($p);

                if ((int) $p->stock < (int) $item->quantity) {
                    $outOfStock[] = $p->name;
                    continue;
                }

                $item->price = $price;
                $item->save();

                $total += $price * (int) $item->quantity;
            }

            if (! empty($outOfStock)) {
                DB::rollBack();

                return back()
                    ->with('error', 'Sorry, not enough stock for: ' . implode(', ', $outOfStock))
                    ->with('out_of_stock', $outOfStock);
            }

            $voucherCode = strtoupper(Str::random(8));

            /** @var Order $order */
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => round($total, 2),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'voucher_code' => $voucherCode,
            ]);

            foreach ($cart->items as $item) {
                /** @var Product|null $p */
                $p = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (! $p) {
                    continue;
                }

                $price = $item->price !== null
                    ? (float) $item->price
                    : $this->unitPrice($p);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'quantity' => (int) $item->quantity,
                    'price' => $price,
                ]);

                if ((int) $p->stock >= (int) $item->quantity) {
                    $p->decrement('stock', (int) $item->quantity);
                } else {
                    DB::rollBack();

                    return back()->with('error', "Stock changed for {$p->name}. Please try again.");
                }

                if (class_exists(StockMovement::class)) {
                    StockMovement::create([
                        'product_id' => $p->id,
                        'quantity' => -(int) $item->quantity,
                        'movement_type' => 'sale',
                        'reference_id' => $order->id,
                        'description' => 'Stock reduced for Order #' . $order->id,
                    ]);
                }
            }

            $cart->status = 'ordered';
            $cart->total_amount = round($total, 2);
            $cart->items()->delete();
            $cart->save();

            session()->forget('cart');

            DB::commit();

            $order->load('items.product', 'user');

            $this->sendOrderEmail($order);
            $this->sendOrderWhatsAppToAdmin($order);

            return redirect()->signedRoute('checkout.thankyou', ['oid' => $order->id]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Order processing failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    /**
     * Send order confirmation email.
     */
    private function sendOrderEmail(Order $order): void
    {
        try {
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)
                    ->cc('deluxeplusmohammad@gmail.com')
                    ->queue(new PurchaseConfirmation($order));
            }
        } catch (\Throwable $e) {
            Log::error('Email send failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send WhatsApp order notification to admin.
     */
    private function sendOrderWhatsAppToAdmin(Order $order): void
    {
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from = config('services.twilio.whatsapp_from');
            $admin = config('services.twilio.admin_whatsapp');

            if (! $sid || ! $token || ! $from || ! $admin) {
                Log::warning('Twilio WhatsApp config is missing', [
                    'sid_exists' => ! empty($sid),
                    'token_exists' => ! empty($token),
                    'from' => $from,
                    'admin' => $admin,
                ]);

                return;
            }

            $client = new Client($sid, $token);

            $productList = '';

            foreach ($order->items as $item) {
                $name = $item->product->name ?? ('Product #' . $item->product_id);
                $quantity = (int) $item->quantity;
                $price = number_format((float) $item->price, 2);

                $productList .= "- {$name} x{$quantity} @ \${$price}\n";
            }

            $customerName = $order->user->name ?? 'N/A';
            $customerPhone = $order->user->phone ?? 'N/A';
            $total = number_format((float) $order->total_amount, 2);

            $message = "New order received on Al Reem Expo.\n\n"
                . "Order ID: #{$order->id}\n"
                . "Customer: {$customerName}\n"
                . "Phone: {$customerPhone}\n"
                . "Payment: {$order->payment_method}\n"
                . "Status: {$order->status}\n"
                . "Total: \${$total}\n"
                . "Voucher: {$order->voucher_code}\n\n"
                . "Shipping Address:\n{$order->shipping_address}\n\n"
                . "Products:\n{$productList}";

            $client->messages->create($this->formatWhatsAppNumber($admin), [
                'from' => $this->formatWhatsAppNumber($from),
                'body' => $message,
            ]);

            Log::info('WhatsApp admin order notification sent', [
                'order_id' => $order->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('WhatsApp admin notification failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format phone number for Twilio WhatsApp.
     */
    private function formatWhatsAppNumber(?string $number): ?string
    {
        if (! $number) {
            return null;
        }

        $number = trim($number);

        if (str_starts_with($number, 'whatsapp:')) {
            return $number;
        }

        if (str_starts_with($number, '0')) {
            $number = '+961' . substr($number, 1);
        }

        if (! str_starts_with($number, '+')) {
            $number = '+961' . $number;
        }

        return 'whatsapp:' . $number;
    }

    private function normalizeLebanesePhone(?string $phone): ?string
{
    if (! $phone) {
        return null;
    }

    $phone = trim($phone);
    $phone = str_replace([' ', '-', '(', ')'], '', $phone);

    // If user writes 03XXXXXX, convert to +9613XXXXXX
    if (str_starts_with($phone, '0')) {
        return '+961' . substr($phone, 1);
    }

    // If user writes 3XXXXXX or 70XXXXXX, convert to +9613XXXXXX / +96170XXXXXX
    if (! str_starts_with($phone, '+')) {
        return '+961' . $phone;
    }

    return $phone;
}
    /**
     * Thank-you page.
     */
    public function thankYou(Request $request)
    {
        $orderId = $request->route('oid') ?? session('order_id');

        if (! $orderId) {
            return redirect()->route('home')->with('error', 'No order found.');
        }

        $order = Order::with('items.product')->find($orderId);

        if (! $order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        $isSigned = $request->hasValidSignature();
        $isOwner = auth()->check() && auth()->id() === (int) $order->user_id;

        if (! $isSigned && ! $isOwner) {
            return redirect()->route('home')->with('error', 'Unauthorized access to order.');
        }

        return view('checkout.thankyou', compact('order'));
    }
    
}