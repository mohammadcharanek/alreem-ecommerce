@extends('layouts.app')

@section('title', 'About Al Reem Expo')

@section('meta_description', 'Learn more about Al Reem Expo, an import and export company sourcing selected products from Canada, USA, and Brazil and selling them in Lebanon.')

@section('content')
<div class="max-w-6xl mx-auto p-6 mt-6">

    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-700 to-green-600 rounded-2xl p-8 md:p-12 text-center mb-12 text-white shadow-sm">
        <h1 class="text-3xl md:text-5xl font-extrabold mb-4">
            About Al Reem Expo
        </h1>

        <p class="text-base md:text-lg text-white/90 max-w-3xl mx-auto leading-8">
            Al Reem Expo is an import and export company that sources selected products from international markets,
            including Canada, the United States of America, and Brazil, and makes them available for customers in Lebanon.
        </p>
    </div>

    {{-- Company Overview --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Who We Are
                </h2>

                <p class="text-gray-700 leading-8 mb-4">
                    Al Reem Expo focuses on connecting the Lebanese market with trusted international products.
                    We work to bring quality items from abroad and provide them locally with reliable service,
                    clear communication, and customer support.
                </p>

                <p class="text-gray-700 leading-8">
                    Our goal is to make imported products more accessible in Lebanon while maintaining a professional
                    shopping experience from product selection to delivery.
                </p>
            </div>

            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="text-xl font-bold text-blue-800 mb-4">
                    Our Import Sources
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-2xl">🇨🇦</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Canada</h4>
                            <p class="text-sm text-gray-600">Selected quality products from Canadian markets.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-2xl">🇺🇸</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">United States</h4>
                            <p class="text-sm text-gray-600">Products sourced from trusted suppliers in the USA.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-2xl">🇧🇷</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Brazil</h4>
                            <p class="text-sm text-gray-600">Imported products from Brazil for the Lebanese market.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-2xl">🇱🇧</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Lebanon</h4>
                            <p class="text-sm text-gray-600">Products are sold and delivered to customers in Lebanon.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Our Mission --}}
    <div class="mb-12 text-center">
        <h2 class="text-3xl font-bold mb-4 text-gray-900">
            Our Mission
        </h2>

        <p class="text-gray-700 max-w-3xl mx-auto leading-8">
            Our mission is to provide customers in Lebanon with access to carefully selected imported products,
            while offering dependable service, honest communication, and a smooth shopping experience.
        </p>
    </div>

    {{-- What We Do --}}
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            What We Do
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="text-4xl mb-4">🌍</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    International Sourcing
                </h3>
                <p class="text-gray-600 leading-7">
                    We source selected products from international markets such as Canada, USA, and Brazil.
                </p>
            </div>

            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="text-4xl mb-4">📦</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    Import & Supply
                </h3>
                <p class="text-gray-600 leading-7">
                    We manage product availability and supply imported items for customers in Lebanon.
                </p>
            </div>

            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="text-4xl mb-4">🚚</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    Local Delivery
                </h3>
                <p class="text-gray-600 leading-7">
                    We sell and deliver products inside Lebanon, depending on stock availability and delivery area.
                </p>
            </div>
        </div>
    </div>

    {{-- Our Values --}}
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            Our Values
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    Quality
                </h3>
                <p class="text-gray-600 leading-7">
                    We aim to provide products that meet customer expectations for value, usefulness, and reliability.
                </p>
            </div>

            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    Trust
                </h3>
                <p class="text-gray-600 leading-7">
                    We believe in honest communication, clear product information, and professional service.
                </p>
            </div>

            <div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-2 text-gray-900">
                    Customer Focus
                </h3>
                <p class="text-gray-600 leading-7">
                    Our customers are our priority, from answering questions to helping them complete their orders.
                </p>
            </div>
        </div>
    </div>

    {{-- Why Choose Us --}}
    <div class="bg-gray-50 rounded-2xl p-6 md:p-8 mb-12 border border-gray-100">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            Why Choose Al Reem Expo?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">
                    Imported Product Selection
                </h3>
                <p class="text-gray-600 leading-7">
                    We focus on providing selected imported products from international markets.
                </p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">
                    Local Availability in Lebanon
                </h3>
                <p class="text-gray-600 leading-7">
                    Products are offered for sale in Lebanon based on stock and shipment availability.
                </p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">
                    Clear Communication
                </h3>
                <p class="text-gray-600 leading-7">
                    We communicate with customers to confirm orders, delivery details, and product availability.
                </p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">
                    Customer Support
                </h3>
                <p class="text-gray-600 leading-7">
                    Our team is ready to help with product questions, order details, shipping, and support.
                </p>
            </div>
        </div>
    </div>

    {{-- Our Team --}}
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-900">
            Meet Our Team
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <img src="{{ asset('storage/CEO.jpeg') }}"
                     alt="Mr. Mohammad Najm - CEO of Al Reem Expo"
                     class="w-32 h-32 mx-auto rounded-full mb-4 object-cover">

                <h3 class="text-xl font-bold mb-1 text-gray-900">
                    Mr. Mohammad Najm
                </h3>

                <p class="text-gray-600">
                    CEO
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <img src="{{ asset('storage/womanbusiness.jpg') }}"
                     alt="Miss Mariam - Team Leader at Al Reem Expo"
                     class="w-32 h-32 mx-auto rounded-full mb-4 object-cover">

                <h3 class="text-xl font-bold mb-1 text-gray-900">
                    Miss Mariam
                </h3>

                <p class="text-gray-600">
                    Team Leader
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <img src="{{ asset('storage/myprofile.jpeg') }}"
                     alt="Mohammad Ali Charanek - Operations Manager at Al Reem Expo"
                     class="w-32 h-32 mx-auto rounded-full mb-4 object-cover">

                <h3 class="text-xl font-bold mb-1 text-gray-900">
                    Mohammad Ali Charanek
                </h3>

                <p class="text-gray-600">
                    Operations Manager
                </p>
            </div>
        </div>
    </div>

    {{-- Call to Action --}}
    <div class="bg-blue-50 rounded-2xl p-8 text-center border border-blue-100">
        <h2 class="text-3xl font-bold mb-4 text-gray-900">
            Get in Touch
        </h2>

        <p class="text-gray-700 mb-6 max-w-2xl mx-auto leading-8">
            Whether you have questions about products, availability, shipping, or orders,
            our team is ready to help you.
        </p>

        <a href="{{ route('contact.show') }}"
           class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-semibold transition">
            Contact Us
        </a>
    </div>

</div>
@endsection