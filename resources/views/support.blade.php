@extends('layouts.app')

@section('title', 'Support | Al Reem Expo')

@section('meta_description', 'Need help with Al Reem Expo? Contact our support team for product questions, order assistance, shipping information, returns, and general support.')

@section('content')
<div class="max-w-6xl mx-auto p-6 mt-6">

    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-700 to-green-600 rounded-2xl p-8 md:p-12 text-center mb-12 text-white shadow-sm">
        <h1 class="text-3xl md:text-5xl font-extrabold mb-4">
            Support Center
        </h1>

        <p class="text-base md:text-lg text-white/90 max-w-3xl mx-auto leading-8">
            Need help with your order, product availability, shipping, or general questions?
            The Al Reem Expo support team is here to assist you.
        </p>
    </div>

    {{-- Support Options --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-4xl mb-4">📦</div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">
                Order Support
            </h2>
            <p class="text-gray-600 leading-7">
                Get help with order confirmation, product availability, payment details, or order updates.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-4xl mb-4">🚚</div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">
                Shipping Support
            </h2>
            <p class="text-gray-600 leading-7">
                Ask about delivery inside Lebanon, shipping fees, estimated delivery time, and shipment status.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="text-4xl mb-4">💬</div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">
                Product Questions
            </h2>
            <p class="text-gray-600 leading-7">
                Contact us if you need more details about imported products, specifications, or availability.
            </p>
        </div>

    </div>

    {{-- Main Support Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            {{-- Left Side --}}
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    How Can We Help?
                </h2>

                <p class="text-gray-700 leading-8 mb-6">
                    Al Reem Expo imports selected products from Canada, the USA, and Brazil, and sells them in Lebanon.
                    Our support team can help you with product questions, order details, shipping information,
                    returns, and general inquiries.
                </p>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <span class="text-green-600 font-bold">✓</span>
                        <p class="text-gray-700">
                            Help with choosing the right product.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-green-600 font-bold">✓</span>
                        <p class="text-gray-700">
                            Order confirmation and order status.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-green-600 font-bold">✓</span>
                        <p class="text-gray-700">
                            Delivery information inside Lebanon.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-green-600 font-bold">✓</span>
                        <p class="text-gray-700">
                            Questions about imported products and availability.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-green-600 font-bold">✓</span>
                        <p class="text-gray-700">
                            Returns, exchanges, and after-sales support.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right Side --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
                <h3 class="text-2xl font-bold text-blue-800 mb-4">
                    Contact Support
                </h3>

                <p class="text-blue-900 leading-8 mb-6">
                    For faster support, please include your name, phone number, order details, and a clear description
                    of your question or issue.
                </p>

                <div class="space-y-4">
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-1">
                            Contact Page
                        </h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Send us your question through our contact form.
                        </p>
                        <a href="{{ route('contact.show') }}"
                           class="inline-flex items-center justify-center bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                            Contact Us
                        </a>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-1">
                            Order Information
                        </h4>
                        <p class="text-gray-600 text-sm">
                            If your question is about an order, please mention your order number or voucher code if available.
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-1">
                            Support Time
                        </h4>
                        <p class="text-gray-600 text-sm">
                            We do our best to reply as soon as possible during working days.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- FAQ Section --}}
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            Common Support Questions
        </h2>

        <div class="space-y-4">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Where does Al Reem Expo get its products from?
                </h3>
                <p class="text-gray-700 leading-8">
                    Al Reem Expo sources selected products from international markets, including Canada, the United States,
                    and Brazil, then sells available products in Lebanon.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Do you deliver outside Lebanon?
                </h3>
                <p class="text-gray-700 leading-8">
                    At the moment, Al Reem Expo focuses on selling and delivering products inside Lebanon only.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    How long does delivery take?
                </h3>
                <p class="text-gray-700 leading-8">
                    Delivery time depends on the area and product availability. In most cases, local delivery inside Lebanon
                    takes around 2 to 5 business days after order confirmation and processing.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    What should I provide when contacting support?
                </h3>
                <p class="text-gray-700 leading-8">
                    Please provide your full name, phone number, order number or voucher code if available,
                    and a clear explanation of your question or issue.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    What if the product I want is not available?
                </h3>
                <p class="text-gray-700 leading-8">
                    Product availability may depend on stock and incoming shipments. You can contact us to ask about
                    restocking or future availability.
                </p>
            </div>

        </div>
    </div>

    {{-- Helpful Links --}}
    <div class="bg-gray-50 rounded-2xl p-6 md:p-8 mb-12 border border-gray-100">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            Helpful Links
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
            <a href="{{ route('products.index') }}"
               class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                <div class="text-3xl mb-2">🛒</div>
                <h3 class="font-bold text-gray-900">Products</h3>
                <p class="text-sm text-gray-600 mt-1">Browse available items</p>
            </a>

            <a href="{{ url('/shipping') }}"
               class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                <div class="text-3xl mb-2">🚚</div>
                <h3 class="font-bold text-gray-900">Shipping</h3>
                <p class="text-sm text-gray-600 mt-1">Delivery information</p>
            </a>

            <a href="{{ url('/returns') }}"
               class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                <div class="text-3xl mb-2">↩️</div>
                <h3 class="font-bold text-gray-900">Returns</h3>
                <p class="text-sm text-gray-600 mt-1">Return policy</p>
            </a>

            <a href="{{ route('contact.show') }}"
               class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                <div class="text-3xl mb-2">📞</div>
                <h3 class="font-bold text-gray-900">Contact</h3>
                <p class="text-sm text-gray-600 mt-1">Send us a message</p>
            </a>
        </div>
    </div>

    {{-- CTA --}}
    <div class="bg-blue-50 rounded-2xl p-8 text-center border border-blue-100">
        <h2 class="text-3xl font-bold mb-4 text-gray-900">
            Still Need Help?
        </h2>

        <p class="text-gray-700 mb-6 max-w-2xl mx-auto leading-8">
            Our support team is ready to help with product questions, shipping details, orders, and after-sales support.
        </p>

        <a href="{{ route('contact.show') }}"
           class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-semibold transition">
            Contact Support
        </a>
    </div>

</div>
@endsection