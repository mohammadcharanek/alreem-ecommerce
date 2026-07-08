<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $this->client = new Client($sid, $token);
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $from = config('services.twilio.from');

            if (! $from) {
                Log::warning('Twilio SMS not sent: missing from number.');
                return false;
            }

            $this->client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Twilio SMS failed: ' . $e->getMessage());

            return false;
        }
    }

    public function sendAdminSms(string $message): bool
    {
        $adminPhone = config('services.twilio.admin_phone');

        if (! $adminPhone) {
            Log::warning('Twilio admin SMS not sent: missing admin phone.');
            return false;
        }

        return $this->sendSms($adminPhone, $message);
    }
}   