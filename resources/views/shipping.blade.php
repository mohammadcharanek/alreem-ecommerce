@extends('layouts.app')

@section('title', 'Shipping Policy | Al Reem Expo')

@section('meta_description', 'Al Reem Expo ships selected products from Canada, USA, and Brazil, and sells and delivers them in Lebanon.')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-700 to-green-600 px-6 py-10 text-center text-white">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-3">
            سياسة الشحن والتوصيل
        </h1>
        <p class="text-white/90 max-w-3xl mx-auto text-sm md:text-base">
            تستورد Al Reem Expo منتجات مختارة من كندا، الولايات المتحدة الأمريكية، والبرازيل، وتوفرها للبيع والتوصيل داخل لبنان.
        </p>
    </div>

    {{-- Arabic Section --}}
    <section dir="rtl" class="px-6 md:px-10 py-10 text-right border-b border-gray-200">
        <div class="max-w-4xl mx-auto space-y-8">

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    مصدر المنتجات
                </h2>
                <p class="text-gray-700 leading-8">
                    تقوم Al Reem Expo باستيراد وتوفير منتجات مختارة من عدة دول، منها كندا، الولايات المتحدة الأمريكية، والبرازيل.
                    يتم عرض المنتجات المتوفرة للبيع داخل لبنان حسب الكمية المتاحة، نوع المنتج، ووقت وصول الشحنة.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    البيع والتوصيل داخل لبنان
                </h2>
                <p class="text-gray-700 leading-8">
                    يتم بيع المنتجات وتوصيلها داخل لبنان فقط في الوقت الحالي. بعد تأكيد الطلب، نقوم بالتواصل مع العميل للتأكد من الاسم، رقم الهاتف، العنوان، والمنطقة قبل تجهيز الطلب للتسليم.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    مدة تجهيز الطلب
                </h2>
                <p class="text-gray-700 leading-8">
                    عادةً يتم تجهيز الطلب خلال 24 إلى 48 ساعة من وقت تأكيده، في حال كان المنتج متوفراً في المخزون داخل لبنان.
                    قد تختلف المدة في حال كان المنتج ضمن شحنة قادمة من الخارج.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    مدة التوصيل داخل لبنان
                </h2>
                <p class="text-gray-700 leading-8">
                    تختلف مدة التوصيل حسب المنطقة وشركة التوصيل. في أغلب الحالات، يتم تسليم الطلب خلال 2 إلى 5 أيام عمل بعد تأكيد الطلب وتجهيزه.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    المنتجات المستوردة من الخارج
                </h2>
                <p class="text-gray-700 leading-8">
                    بعض المنتجات قد تكون مستوردة من كندا، الولايات المتحدة الأمريكية، أو البرازيل. في هذه الحالة، قد يختلف وقت التوفر أو التسليم حسب موعد وصول الشحنة، إجراءات الشحن، والتخليص.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    رسوم الشحن
                </h2>
                <p class="text-gray-700 leading-8">
                    يتم تحديد رسوم التوصيل داخل لبنان حسب موقع العميل، حجم الطلب، وزن المنتجات، وطريقة التوصيل المتاحة.
                    قد يتم إبلاغ العميل بقيمة التوصيل قبل تأكيد الطلب النهائي.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    معلومات العنوان
                </h2>
                <p class="text-gray-700 leading-8">
                    يرجى التأكد من إدخال الاسم الكامل، رقم الهاتف، العنوان الصحيح، والمنطقة بشكل واضح.
                    Al Reem Expo غير مسؤولة عن أي تأخير ناتج عن معلومات غير صحيحة أو ناقصة.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    التأخير في الشحن أو التوصيل
                </h2>
                <p class="text-gray-700 leading-8">
                    قد يحدث تأخير خارج عن إرادتنا بسبب ظروف الشحن الدولي، الجمارك، الأحوال الجوية، العطل الرسمية، أو مشاكل لدى شركة التوصيل.
                    سنحاول دائماً متابعة الطلب وإبلاغ العميل بأي تحديثات مهمة.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    الاستلام والتحقق من الطلب
                </h2>
                <p class="text-gray-700 leading-8">
                    عند استلام الطلب، يرجى التحقق من المنتجات وسلامة التغليف. في حال وجود أي مشكلة، يرجى التواصل معنا في أقرب وقت ممكن.
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                <h2 class="text-xl font-bold text-blue-800 mb-2">
                    ملاحظة مهمة
                </h2>
                <p class="text-blue-900 leading-8">
                    توفر المنتجات ومدة التوصيل قد تختلف حسب بلد المصدر، نوع المنتج، المخزون المتوفر، وشركة الشحن أو التوصيل.
                    للتأكد من تفاصيل الطلب، يرجى التواصل معنا قبل إتمام عملية الشراء.
                </p>
            </div>

        </div>
    </section>

    {{-- English Section --}}
    <section class="px-6 md:px-10 py-10">
        <div class="max-w-4xl mx-auto space-y-8">

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Product Sources
                </h2>
                <p class="text-gray-700 leading-8">
                    Al Reem Expo imports and provides selected products from different countries, including Canada, the United States of America, and Brazil.
                    Products are offered for sale in Lebanon depending on availability, product type, and shipment arrival time.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Sales and Delivery in Lebanon
                </h2>
                <p class="text-gray-700 leading-8">
                    At the moment, Al Reem Expo sells and delivers products inside Lebanon only.
                    After an order is confirmed, we may contact the customer to verify the name, phone number, address, and delivery area before preparing the order.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Order Processing Time
                </h2>
                <p class="text-gray-700 leading-8">
                    Orders are usually prepared within 24 to 48 hours after confirmation if the product is available in stock in Lebanon.
                    Processing time may vary if the product is part of an incoming international shipment.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Delivery Time in Lebanon
                </h2>
                <p class="text-gray-700 leading-8">
                    Delivery time may vary depending on the customer’s area and the delivery provider.
                    In most cases, orders are delivered within 2 to 5 business days after confirmation and processing.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Imported Products
                </h2>
                <p class="text-gray-700 leading-8">
                    Some products may be imported from Canada, the USA, or Brazil.
                    In these cases, availability and delivery time may depend on shipment schedules, international shipping procedures, and customs clearance.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Shipping Fees
                </h2>
                <p class="text-gray-700 leading-8">
                    Delivery fees inside Lebanon depend on the customer’s location, order size, product weight, and available delivery method.
                    The customer may be informed of the delivery fee before final order confirmation.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Address Information
                </h2>
                <p class="text-gray-700 leading-8">
                    Please make sure to provide your full name, phone number, correct address, and delivery area clearly.
                    Al Reem Expo is not responsible for delays caused by incorrect or incomplete address information.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Shipping or Delivery Delays
                </h2>
                <p class="text-gray-700 leading-8">
                    Delays may happen due to international shipping conditions, customs procedures, weather conditions, public holidays, or delivery company issues.
                    We will do our best to follow up on the order and inform the customer of any important updates.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                    Receiving Your Order
                </h2>
                <p class="text-gray-700 leading-8">
                    When receiving your order, please check the products and packaging condition.
                    If there is any issue, please contact us as soon as possible.
                </p>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-5">
                <h2 class="text-xl font-bold text-green-800 mb-2">
                    Important Note
                </h2>
                <p class="text-green-900 leading-8">
                    Product availability and delivery time may vary depending on the source country, product type, available stock, and shipping or delivery provider.
                    For exact order details, please contact us before completing your purchase.
                </p>
            </div>

        </div>
    </section>
</div>
@endsection