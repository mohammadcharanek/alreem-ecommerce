<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class TwilioWhatsAppService
{
    private Client $client;

    private string $from;

    public function __construct()
    {
        $sid = trim((string) config('services.twilio.sid'));
        $token = trim((string) config('services.twilio.token'));
        $this->from = $this->normalizePhone(
            (string) config('services.twilio.whatsapp_from')
        );

        if ($sid === '' || $token === '') {
            throw new InvalidArgumentException(
                'Twilio Account SID or Auth Token is missing.'
            );
        }

        if (! preg_match('/^AC[a-f0-9]{32}$/i', $sid)) {
            throw new InvalidArgumentException(
                'Twilio Account SID has an invalid format.'
            );
        }

        if ($this->from === '') {
            throw new InvalidArgumentException(
                'Twilio WhatsApp sender number is missing or invalid.'
            );
        }

        $this->client = new Client($sid, $token);
    }

    public function send(string $recipient, string $message): bool
    {
        $recipient = $this->normalizePhone($recipient);
        $message = trim($message);

        if ($recipient === '') {
            Log::warning('Twilio WhatsApp recipient number is invalid.');

            return false;
        }

        if ($message === '') {
            Log::warning('Twilio WhatsApp message body is empty.', [
                'recipient' => $this->maskPhone($recipient),
            ]);

            return false;
        }

        try {
            $twilioMessage = $this->client->messages->create(
                'whatsapp:' . $recipient,
                [
                    'from' => 'whatsapp:' . $this->from,
                    'body' => $message,
                ]
            );

            Log::info('Twilio WhatsApp message created.', [
                'message_sid' => $twilioMessage->sid,
                'recipient' => $this->maskPhone($recipient),
                'status' => $twilioMessage->status,
            ]);

            return true;
        } catch (RestException $exception) {
            Log::error('Twilio WhatsApp message failed.', [
                'recipient' => $this->maskPhone($recipient),
                'http_status' => $exception->getStatusCode(),
                'twilio_code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);

            return false;
        } catch (Throwable $exception) {
            Log::error('Twilio WhatsApp message failed unexpectedly.', [
                'recipient' => $this->maskPhone($recipient),
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);

            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);

        if ($phone === '') {
            return '';
        }

        $phone = preg_replace('/^whatsapp:/i', '', $phone) ?? $phone;
        $phone = preg_replace('/[^\d+]/', '', $phone) ?? $phone;

        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        } elseif (str_starts_with($phone, '0')) {
            $phone = '+961' . substr($phone, 1);
        } elseif (! str_starts_with($phone, '+')) {
            $phone = '+961' . $phone;
        }

        if (! preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            return '';
        }

        return $phone;
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($phone) - 4)
            . substr($phone, -4);
    }
}
