<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    protected ?Client $client = null;

    public function __construct()
    {
        $sid = trim((string) config('services.twilio.sid'));
        $token = trim((string) config('services.twilio.token'));

        if ($sid === '' || $token === '') {
            Log::warning(
                'Twilio SMS is not configured; messages will be skipped.',
                [
                    'configuration_keys' => [
                        'account_sid',
                        'auth_token',
                    ],
                ]
            );

            return;
        }

        $this->client = $this->createClient($sid, $token);
    }

    /**
     * Create the SDK client. Kept as a seam for no-network tests.
     */
    protected function createClient(string $sid, string $token): Client
    {
        return new Client($sid, $token);
    }

    public function sendSms(string $to, string $message): bool
    {
        if ($this->client === null) {
            return false;
        }

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
            Log::error('Twilio SMS failed.', [
                'exception' => get_class($e),
            ]);

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
