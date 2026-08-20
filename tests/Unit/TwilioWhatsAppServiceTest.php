<?php

namespace Tests\Unit;

use App\Services\TwilioWhatsAppService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Twilio\AuthStrategy\AuthStrategy;
use Twilio\Http\Client as TwilioHttpClient;
use Twilio\Http\Response;
use Twilio\Rest\Client;
use RuntimeException;

class TwilioWhatsAppServiceTest extends TestCase
{
    public const ACCOUNT_SID = 'ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    private const CONTENT_SID = 'HXbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'services.twilio.sid' => self::ACCOUNT_SID,
            'services.twilio.token' => 'test-auth-token',
            'services.twilio.whatsapp_from' =>
                'whatsapp:whatsapp:+961 3 123 456',
            'services.twilio.whatsapp_content_sid' => self::CONTENT_SID,
        ]);
    }

    public function test_it_adds_the_whatsapp_prefix_exactly_once(): void
    {
        $http = new RecordingTwilioHttpClient;
        $service = $this->serviceUsing($http);

        $sent = $service->send(
            'whatsapp:whatsapp:+961 70 123 456',
            'Test message'
        );

        $this->assertTrue($sent);
        $this->assertCount(1, $http->requests);
        $this->assertSame(
            'whatsapp:+96170123456',
            $http->requests[0]['data']['To']
        );
        $this->assertSame(
            'whatsapp:+9613123456',
            $http->requests[0]['data']['From']
        );
    }

    public function test_it_sends_template_data_through_the_fake_transport(): void
    {
        $http = new RecordingTwilioHttpClient;
        $service = $this->serviceUsing($http);

        $sent = $service->sendTemplate(
            '0096170123456',
            ['1' => ' Alice ']
        );

        $this->assertTrue($sent);
        $this->assertCount(1, $http->requests);
        $this->assertSame(
            self::CONTENT_SID,
            $http->requests[0]['data']['ContentSid']
        );
        $this->assertSame(
            '{"1":"Alice"}',
            $http->requests[0]['data']['ContentVariables']
        );
    }

    public function test_missing_configuration_skips_sending_and_logs_only_keys(): void
    {
        $privateSid = self::ACCOUNT_SID;
        $privateToken = 'private-auth-token';

        config()->set([
            'services.twilio.sid' => $privateSid,
            'services.twilio.token' => '',
            'services.twilio.whatsapp_from' => '',
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use (
                $privateSid,
                $privateToken
            ): bool {
                $logged = $message . json_encode($context);

                return str_contains($message, 'not configured')
                    && $context['configuration_keys'] === [
                        'auth_token',
                        'whatsapp_from',
                    ]
                    && ! str_contains($logged, $privateSid)
                    && ! str_contains($logged, $privateToken);
            });

        $http = new RecordingTwilioHttpClient;
        $service = $this->serviceUsing($http);

        $this->assertFalse(
            $service->send('whatsapp:+96170123456', 'Test message')
        );
        $this->assertSame([], $http->requests);
    }

    public function test_invalid_content_sid_is_not_logged_or_sent(): void
    {
        $privateContentSid = 'HX-private-invalid-content-sid';
        config()->set(
            'services.twilio.whatsapp_content_sid',
            $privateContentSid
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (string $message, array $context) use (
                $privateContentSid
            ): bool {
                $logged = $message . json_encode($context);

                return str_contains($message, 'invalid format')
                    && $context['configuration_key']
                        === 'whatsapp_content_sid'
                    && ! str_contains($logged, $privateContentSid);
            });

        $http = new RecordingTwilioHttpClient;
        $service = $this->serviceUsing($http);

        $this->assertFalse(
            $service->sendTemplate('whatsapp:+96170123456')
        );
        $this->assertSame([], $http->requests);
    }

    public function test_exception_messages_cannot_leak_credentials_to_logs(): void
    {
        $privateSid = self::ACCOUNT_SID;
        $privateToken = 'private-auth-token';

        config()->set('services.twilio.token', $privateToken);

        $http = new RecordingTwilioHttpClient;
        $http->exception = new RuntimeException(
            "Request failed for {$privateSid} using {$privateToken}"
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (string $message, array $context) use (
                $privateSid,
                $privateToken
            ): bool {
                $logged = $message . json_encode($context);

                return str_contains($message, 'failed unexpectedly')
                    && ! str_contains($logged, $privateSid)
                    && ! str_contains($logged, $privateToken);
            });

        $service = $this->serviceUsing($http);

        $this->assertFalse(
            $service->send('+96170123456', 'Test message')
        );
    }

    private function serviceUsing(
        RecordingTwilioHttpClient $http
    ): TwilioWhatsAppService {
        $client = new Client(
            self::ACCOUNT_SID,
            'test-auth-token',
            null,
            null,
            $http
        );

        return new class($client) extends TwilioWhatsAppService
        {
            public function __construct(private Client $fakeClient)
            {
                parent::__construct();
            }

            protected function createClient(
                string $sid,
                string $token
            ): Client {
                return $this->fakeClient;
            }
        };
    }
}

class RecordingTwilioHttpClient implements TwilioHttpClient
{
    /** @var list<array{method: string, url: string, data: array}> */
    public array $requests = [];

    public ?\Throwable $exception = null;

    public function request(
        string $method,
        string $url,
        array $params = [],
        array $data = [],
        array $headers = [],
        ?string $user = null,
        ?string $password = null,
        ?int $timeout = null,
        ?AuthStrategy $authStrategy = null
    ): Response {
        if ($this->exception !== null) {
            throw $this->exception;
        }

        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'data' => $data,
        ];

        return new Response(201, json_encode([
            'sid' => 'SMcccccccccccccccccccccccccccccccc',
            'status' => 'queued',
            'direction' => 'outbound-api',
            'account_sid' => TwilioWhatsAppServiceTest::ACCOUNT_SID,
            'to' => $data['To'] ?? null,
            'from' => $data['From'] ?? null,
            'body' => $data['Body'] ?? null,
            'num_segments' => '1',
            'num_media' => '0',
            'api_version' => '2010-04-01',
            'subresource_uris' => [],
        ], JSON_THROW_ON_ERROR));
    }
}
