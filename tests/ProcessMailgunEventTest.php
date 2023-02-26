<?php


namespace Aleahy\MailgunLogger\Test;


use Aleahy\MailgunLogger\Exceptions\WebhookFailed;
use Aleahy\MailgunLogger\ProcessMailgunEventJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessMailgunEventTest extends TestCase
{
    /**
     * @test
     */
    public function test_it_handles_delivered_messages()
    {
        config(['mailgun-logger.jobs' => [
            'delivered' => DeliveredJob::class
        ]]);
        $eventData = $this->getFileStub('DeliveredMessageEvent.txt');
        $webhookCall = WebhookCall::create([
            'name' => 'mailgun',
            'payload' => [
                'event-data' => $eventData
            ],
            'url' => 'https://test.com',
        ]);
        $job = (new ProcessMailgunEventJob($webhookCall));
        $job->handle();

        $this->assertEquals($webhookCall->id, cache('deliveredJob')->id);
    }

    /**
     * @test
     */
    public function test_throws_exception_if_missing_event_attribute()
    {
        $this->expectException(WebhookFailed::class);

        $webhookCall = WebhookCall::create([
            'name' => 'mailgun',
            'payload' => [
                'event-data' => []
            ],
            'url' => 'https://test.com',
        ]);

        $job = (new ProcessMailgunEventJob($webhookCall));
        $job->handle();
    }

    /**
     * @test
     */
    public function test_it_throws_an_exception_if_job_class_does_not_exist()
    {
        $this->expectException(WebhookFailed::class);

        config(['mailgun-logger.jobs' => [
            'delivered' => 'JobClassDoesNotExist'
        ]]);
        $eventData = $this->getFileStub('DeliveredMessageEvent.txt');
        $webhookCall = WebhookCall::create([
            'name' => 'mailgun',
            'payload' => [
                'event-data' => $eventData
            ],
            'url' => 'https://test.com',
        ]);
        $job = (new ProcessMailgunEventJob($webhookCall));
        $job->handle();
    }
}