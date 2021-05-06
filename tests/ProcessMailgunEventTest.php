<?php


namespace Aleahy\MailgunLogger\Test;


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
            ]
        ]);
        $job = (new ProcessMailgunEventJob($webhookCall));
        $job->handle();

        $this->assertEquals($webhookCall->id, cache('deliveredJob')->id);
    }
}