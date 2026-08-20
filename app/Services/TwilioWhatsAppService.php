<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class TwilioWhatsAppService
{
    private ?Client $client = null;

    private string $from;

    private string $contentSid;

    /** @var list<string> */
    private array $configurationIssues = [];

    public function __construct()
    {
        $sid = trim((string) config('services.twilio.sid'));
        $token = trim((string) config('services.twilio.token'));

        $this->from = $this->normalizePhone(
            (string) config('services.twilio.whatsapp_from')
        );

        $this->contentSid = trim(
            (string) config('services.twilio.whatsapp_content_sid')
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Twilio credentials
        |--------------------------------------------------------------------------
        */

        if ($sid === '') {
            $this->configurationIssues[] = 'account_sid';
        } elseif (! preg_match('/^AC[a-f0-9]{32}$/i', $sid)) {
            $this->configurationIssues[] = 'account_sid_format';
        }

        if ($token === '') {
            $this->configurationIssues[] = 'auth_token';
        }

        /*
        |--------------------------------------------------------------------------
        | Validate WhatsApp sender
        |--------------------------------------------------------------------------
        */

        if ($this->from === '') {
            $this->configurationIssues[] = 'whatsapp_from';
        }

        if ($this->configurationIssues !== []) {
            Log::warning(
                'Twilio WhatsApp is not configured; messages will be skipped.',
                [
                    'configuration_keys' => $this->configurationIssues,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Twilio client
        |--------------------------------------------------------------------------
        */

        $this->client = $this->createClient($sid, $token);
    }

    /**
     * Create the SDK client. Kept as a seam for no-network tests.
     */
    protected function createClient(string $sid, string $token): Client
    {
        return new Client($sid, $token);
    }

    /**
     * Send a normal/free-form WhatsApp message.
     *
     * Keep this method because existing application code may already call:
     *
     * $twilio->send($phone, $message);
     *
     * IMPORTANT:
     * Free-form WhatsApp messages are generally intended for an active
     * WhatsApp customer-service conversation window.
     */
    public function send(string $recipient, string $message): bool
    {
        $recipient = $this->normalizePhone($recipient);
        $message = trim($message);

        if ($this->client === null) {
            return false;
        }

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

            Log::info('Twilio WhatsApp free-form message created.', [
                'message_sid' => $twilioMessage->sid,
                'recipient' => $this->maskPhone($recipient),
                'status' => $twilioMessage->status,
            ]);

            return true;
        } catch (RestException $exception) {
            $this->logRestException(
                $exception,
                $recipient,
                'Twilio WhatsApp free-form message failed.'
            );

            return false;
        } catch (Throwable $exception) {
            $this->logUnexpectedException(
                $exception,
                $recipient,
                'Twilio WhatsApp free-form message failed unexpectedly.'
            );

            return false;
        }
    }

    /**
     * Send the approved WhatsApp Content Template.
     *
     * Example:
     *
     * $twilio->sendTemplate($phone, [
     *     '1' => 'Mohammad',
     *     '2' => '1234',
     * ]);
     *
     * The keys MUST match the placeholders in the approved template:
     *
     * {{1}}, {{2}}, {{3}}, ...
     */
    public function sendTemplate(
        string $recipient,
        array $variables = [],
        ?string $contentSid = null
    ): bool {
        $recipient = $this->normalizePhone($recipient);

        $contentSid = trim(
            $contentSid ?: $this->contentSid
        );

        if ($this->client === null) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate recipient
        |--------------------------------------------------------------------------
        */

        if ($recipient === '') {
            Log::warning(
                'Twilio WhatsApp template recipient number is invalid.'
            );

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Content SID
        |--------------------------------------------------------------------------
        */

        if ($contentSid === '') {
            Log::error('Twilio WhatsApp Content SID is missing.', [
                'recipient' => $this->maskPhone($recipient),
                'configuration_key' => 'whatsapp_content_sid',
            ]);

            return false;
        }

        if (! preg_match('/^HX[a-f0-9]{32}$/i', $contentSid)) {
            Log::error('Twilio WhatsApp Content SID has an invalid format.', [
                'recipient' => $this->maskPhone($recipient),
                'configuration_key' => 'whatsapp_content_sid',
            ]);

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Clean template variables
        |--------------------------------------------------------------------------
        |
        | Twilio expects ContentVariables as a JSON object.
        |
        | Example:
        |
        | {
        |     "1": "Mohammad",
        |     "2": "1234"
        | }
        |
        */

        $cleanVariables = [];

        foreach ($variables as $key => $value) {
            $key = trim((string) $key);

            if ($key === '') {
                continue;
            }

            /*
             * Content Template values should be strings.
             *
             * Null is converted to an empty string rather than letting PHP
             * produce unexpected JSON values.
             */
            $cleanVariables[$key] = $value === null
                ? ''
                : trim((string) $value);
        }

        /*
        |--------------------------------------------------------------------------
        | Build Twilio request
        |--------------------------------------------------------------------------
        */

        $options = [
            'from' => 'whatsapp:' . $this->from,
            'contentSid' => $contentSid,
        ];

        /*
         * Do not send ContentVariables when the approved template contains
         * no variables.
         */
        if ($cleanVariables !== []) {
            try {
                $options['contentVariables'] = json_encode(
                    $cleanVariables,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
                );
            } catch (Throwable $exception) {
                Log::error(
                    'Twilio WhatsApp template variables could not be encoded.',
                    [
                        'recipient' => $this->maskPhone($recipient),
                        'message' => $exception->getMessage(),
                    ]
                );

                return false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Send approved template
        |--------------------------------------------------------------------------
        */

        try {
            $twilioMessage = $this->client->messages->create(
                'whatsapp:' . $recipient,
                $options
            );

            Log::info('Twilio WhatsApp template message created.', [
                'message_sid' => $twilioMessage->sid,
                'recipient' => $this->maskPhone($recipient),
                'status' => $twilioMessage->status,
                'variable_keys' => array_keys($cleanVariables),
            ]);

            return true;
        } catch (RestException $exception) {
            $this->logRestException(
                $exception,
                $recipient,
                'Twilio WhatsApp template message failed.',
                [
                    'variable_keys' => array_keys($cleanVariables),
                ]
            );

            return false;
        } catch (Throwable $exception) {
            $this->logUnexpectedException(
                $exception,
                $recipient,
                'Twilio WhatsApp template message failed unexpectedly.',
                [
                    'variable_keys' => array_keys($cleanVariables),
                ]
            );

            return false;
        }
    }

    /**
     * Normalize a Lebanese/international phone number into E.164 format.
     *
     * Examples:
     *
     * 03 123 456       -> +9613123456
     * 03123456         -> +9613123456
     * 9613123456       -> +9613123456
     * +9613123456      -> +9613123456
     * 009613123456     -> +9613123456
     */
    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);

        if ($phone === '') {
            return '';
        }

        /*
         * Remove an existing WhatsApp prefix if one was supplied.
         */
        $phone = preg_replace(
            '/^whatsapp:/i',
            '',
            $phone
        ) ?? $phone;

        /*
         * Remove spaces, brackets, dashes, etc.
         * Preserve only digits and "+".
         */
        $phone = preg_replace(
            '/[^\d+]/',
            '',
            $phone
        ) ?? $phone;

        /*
         * International format beginning with 00.
         *
         * Example:
         * 009613123456 -> +9613123456
         */
        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        /*
         * Lebanese local format.
         *
         * Example:
         * 03123456 -> +9613123456
         */
        elseif (str_starts_with($phone, '0')) {
            $phone = '+961' . substr($phone, 1);
        }

        /*
         * Lebanese number containing country code but no "+".
         *
         * Example:
         * 9613123456 -> +9613123456
         */
        elseif (str_starts_with($phone, '961')) {
            $phone = '+' . $phone;
        }

        /*
         * Number without a country code.
         *
         * Example:
         * 3123456 -> +9613123456
         */
        elseif (! str_starts_with($phone, '+')) {
            $phone = '+961' . $phone;
        }

        /*
         * General E.164 validation.
         */
        if (! preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            return '';
        }

        return $phone;
    }

    /**
     * Hide most of a phone number from application logs.
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($phone) - 4)
            . substr($phone, -4);
    }

    /**
     * Log Twilio REST/API errors safely.
     */
    private function logRestException(
        RestException $exception,
        string $recipient,
        string $message,
        array $context = []
    ): void {
        Log::error($message, array_merge([
            'recipient' => $this->maskPhone($recipient),
            'http_status' => $exception->getStatusCode(),
            'twilio_code' => $exception->getCode(),
            'exception' => get_class($exception),
        ], $context));
    }

    /**
     * Log unexpected application/runtime errors safely.
     */
    private function logUnexpectedException(
        Throwable $exception,
        string $recipient,
        string $message,
        array $context = []
    ): void {
        Log::error($message, array_merge([
            'recipient' => $this->maskPhone($recipient),
            'exception' => get_class($exception),
        ], $context));
    }
}
