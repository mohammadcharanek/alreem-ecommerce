<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores credentials and configuration for third-party services
    | such as Twilio, Postmark, AWS, Google and others.
    |
    */

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),

        'from' => env('TWILIO_SMS_FROM'),
        'admin_phone' => env('TWILIO_ADMIN_PHONE'),

        /*
        |--------------------------------------------------------------------------
        | WhatsApp
        |--------------------------------------------------------------------------
        */

        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),

        /*
         * Approved Al Reem Expo order confirmation template.
         */
        'whatsapp_content_sid' => env(
            'TWILIO_WHATSAPP_ORDER_CONTENT_SID'
        ),

        /*
         * Administrator WhatsApp number.
         */
        'admin_whatsapp' => env(
            'TWILIO_ADMIN_WHATSAPP'
        ),

        /*
         * Enable/disable administrator WhatsApp notifications.
         */
        'send_admin_whatsapp' => env(
            'TWILIO_SEND_ADMIN_WHATSAPP',
            false
        ),

        /*
         * Enable/disable customer WhatsApp notifications.
         */
        'send_customer_whatsapp' => env(
            'TWILIO_SEND_CUSTOMER_WHATSAPP',
            false
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Postmark
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Resend
    |--------------------------------------------------------------------------
    */

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Amazon SES
    |--------------------------------------------------------------------------
    */

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env(
            'AWS_DEFAULT_REGION',
            'us-east-1'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slack
    |--------------------------------------------------------------------------
    */

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env(
                'SLACK_BOT_USER_OAUTH_TOKEN'
            ),

            'channel' => env(
                'SLACK_BOT_USER_DEFAULT_CHANNEL'
            ),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id' => env(
            'GOOGLE_CLIENT_ID'
        ),

        'client_secret' => env(
            'GOOGLE_CLIENT_SECRET'
        ),

        'redirect' => env(
            'GOOGLE_REDIRECT_URI'
        ),
    ],

];
