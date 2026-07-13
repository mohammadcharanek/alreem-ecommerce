@component('mail::message')
@if($isAdminCopy)
# New order received: #{{ $order->id }}
@else
# Thank you for your order, {{ $order->user->name ?? 'Customer' }} 🎉

We’ve received your order **#{{ $order->id }}**.
@endif

## Order Details

@component('mail::table')
| Product | Qty | Unit Price |
|:--|:--:|--:|
@foreach($order->items as $item)
| {{ $item->product->name ?? ('Product #'.$item->product_id) }} | {{ $item->quantity }} | ${{ number_format((float) $item->price, 2) }} |
@endforeach
@endcomponent

**Total:** ${{ number_format((float) $order->total_amount, 2) }}

## Customer and Delivery Information

**Customer:** {{ $order->user->name ?? 'Customer' }}

**Email:** {{ $order->user->email ?? 'Not provided' }}

**Phone:** {{ $order->user->phone ?? 'Not provided' }}

**Shipping Address:**  
{{ $order->shipping_address ?: 'Not provided' }}

**Payment Method:**  
{{ $order->payment_method
    ? ucwords(str_replace(['_', '-'], ' ', $order->payment_method))
    : 'Not provided'
}}

**Order Status:**  
{{ ucwords(str_replace(['_', '-'], ' ', $order->status ?? 'pending')) }}

@if(! $isAdminCopy)
@component('mail::button', ['url' => route('orders.index')])
View My Orders
@endcomponent
@endif

@if($isAdminCopy)
This message was sent to the Al Reem Expo administrator.
@else
Thanks for shopping with Al Reem Expo!
@endif

Regards,  
**Al Reem Expo**

@endcomponent
