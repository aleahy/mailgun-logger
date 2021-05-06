<?php


namespace Aleahy\MailgunLogger\Test;


use Illuminate\Support\Arr;

class WebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['mailgun-logger.signing_secret' => 'abc123']);
    }

    /**
     * @test
     */
    public function test_it_receives_the_webhook()
    {
        $this->withoutExceptionHandling();
        $this->assertDatabaseCount('webhook_calls', 0);
        $payload = [
            'event-data' => [
                'event' => 'something',
                'type' => 'Delivered Messages',
                'key' => 'some_key'
            ],
        ];
        $payload['signature'] = $this->createMailgunSignature();

        $response = $this->post(route('mailgunlogger.webhooks'), $payload)
            ->assertSuccessful();
        $this->assertDatabaseCount('webhook_calls', 1);
    }
}