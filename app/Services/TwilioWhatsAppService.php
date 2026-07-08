<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('twilio.account_sid'),
            config('twilio.auth_token')
        );
    }

    public function send(string $to, string $message): bool
    {
        try {
            $this->client->messages->create($this->formatWhatsAppNumber($to), [
                'from' => config('twilio.whatsapp_from'),
                'body' => $message,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Twilio WhatsApp message failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function formatWhatsAppNumber(string $number): string
    {
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
}