@extends('layouts.app')

@section('title', 'FAQ - Al Reem Expo')

@section('content')
<div class="bg-gray-50 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Hero Section --}}
        <div class="bg-blue-50 rounded-2xl p-8 text-center mb-10 border border-blue-100">
            <h1 class="text-3xl sm:text-4xl font-bold text-blue-800 mb-4">
                Frequently Asked Questions
            </h1>
            <p class="text-gray-700 max-w-2xl mx-auto">
                Find answers to common questions about Al Reem Expo, our products, orders,
                delivery, payments, and support.
            </p>
        </div>

        {{-- FAQ Intro --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-3">
                How can we help you?
            </h2>
            <p class="text-gray-600 max-w-3xl mx-auto">
                Al Reem Expo supplies quality imported products with a focus on reliability,
                professional service, and customer satisfaction.
            </p>
        </div>

        {{-- FAQ Accordion --}}
        <div class="space-y-4">

            {{-- Question 1 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    What is Al Reem Expo?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Al Reem Expo is a trading and supply company that focuses on importing
                    and providing quality products to customers and businesses. Our goal is
                    to offer reliable products, smooth ordering, and trusted service.
                </p>
            </details>

            {{-- Question 2 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    What type of products do you provide?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    We provide a variety of imported products depending on availability and
                    market demand. Product categories may include professional supplies,
                    equipment, accessories, and other selected imported goods.
                </p>
            </details>

            {{-- Question 3 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    How can I place an order?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    You can browse the available products on our website, add items to your
                    cart, and continue to checkout. After submitting your order, our team may
                    contact you to confirm the details if needed.
                </p>
            </details>

            {{-- Question 4 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    Do I need an account to order?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Some features may be available without an account, but creating an account
                    helps you track your orders, manage your information, and enjoy a smoother
                    shopping experience.
                </p>
            </details>

            {{-- Question 5 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    What payment methods are available?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Available payment methods may include cash on delivery or other local
                    payment options, depending on your location and the order details.
                    Payment options will be confirmed during checkout or by our team.
                </p>
            </details>

            {{-- Question 6 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    Do you offer delivery?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Yes, delivery may be available depending on the destination and the type
                    of order. Delivery time and cost can vary based on location, product size,
                    and availability.
                </p>
            </details>

            {{-- Question 7 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    How long does delivery take?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Delivery time depends on your location and product availability. In-stock
                    items are usually processed faster, while special or imported items may
                    require additional time.
                </p>
            </details>

            {{-- Question 8 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    Can I return or exchange a product?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Returns and exchanges depend on the product condition, reason for return,
                    and our return policy. Products should usually be unused, in original
                    condition, and returned within the allowed period.
                </p>
            </details>

            {{-- Question 9 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    What should I do if I receive a damaged or incorrect item?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Please contact us as soon as possible with your order details and clear
                    photos of the product. Our team will review the issue and guide you with
                    the next steps.
                </p>
            </details>

            {{-- Question 10 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    Are all products always available?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Product availability may change depending on stock, supplier availability,
                    and import timing. If a product is unavailable, our team may suggest an
                    alternative or inform you when it becomes available again.
                </p>
            </details>

            {{-- Question 11 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    Do you provide wholesale or business orders?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Yes, business and bulk orders may be available for selected products.
                    You can contact us with the product name, quantity, and your requirements
                    so we can provide more details.
                </p>
            </details>

            {{-- Question 12 --}}
            <details class="group bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <summary class="flex cursor-pointer items-center justify-between font-semibold text-gray-800">
                    How can I contact Al Reem Expo?
                    <span class="ml-4 text-blue-700 transition group-open:rotate-180">
                        ▼
                    </span>
                </summary>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    You can contact us through the contact page, phone, WhatsApp, or email
                    depending on the contact options available on our website.
                </p>
            </details>

        </div>

        {{-- Contact CTA --}}
        <div class="mt-12 bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">
                Still have a question?
            </h2>
            <p class="text-gray-600 mb-6">
                Our team is ready to help you with product information, order support,
                delivery details, and business inquiries.
            </p>

            <a href="{{ url('/contact') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition">
                Contact Us
            </a>
        </div>

    </div>
</div>
@endsection