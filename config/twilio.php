<?php

return [
    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'sms_from' => env('TWILIO_SMS_FROM'),
    'admin_phone' => env('TWILIO_ADMIN_PHONE'),
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    'admin_whatsapp_number' => env('TWILIO_ADMIN_WHATSAPP'),
    'whatsapp_order_content_sid' => env(
        'TWILIO_WHATSAPP_ORDER_CONTENT_SID'
    ),
    'send_admin_whatsapp' => env(
        'TWILIO_SEND_ADMIN_WHATSAPP',
        false
    ),
    'send_customer_whatsapp' => env(
        'TWILIO_SEND_CUSTOMER_WHATSAPP',
        false
    ),
];
