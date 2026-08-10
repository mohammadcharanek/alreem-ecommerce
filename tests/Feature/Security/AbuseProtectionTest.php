<?php

namespace Tests\Feature\Security;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AbuseProtectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function test_contact_endpoint_throttles_repeated_requests(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
                ->post(route('contact.submit'), [])
                ->assertSessionHasErrors();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->post(route('contact.submit'), [])
            ->assertTooManyRequests();
    }

    public function test_legitimate_contact_request_still_works(): void
    {
        Mail::fake();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20'])
            ->from(route('contact.show'))
            ->post(route('contact.submit'), [
                'name' => 'Customer',
                'email' => 'customer@example.test',
                'subject' => 'Product question',
                'message' => 'Is this product currently available?',
            ])
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('success');
    }

    public function test_oversized_contact_message_is_rejected(): void
    {
        Mail::fake();

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.30'])
            ->from(route('contact.show'))
            ->post(route('contact.submit'), [
                'name' => 'Customer',
                'email' => 'customer@example.test',
                'subject' => 'Product question',
                'message' => str_repeat('A', 5001),
            ])
            ->assertRedirect(route('contact.show'))
            ->assertSessionHasErrors('message');
    }
}
