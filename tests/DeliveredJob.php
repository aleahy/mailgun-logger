<?php


namespace Aleahy\MailgunLogger\Test;


use Spatie\WebhookClient\ProcessWebhookJob;

class DeliveredJob extends ProcessWebhookJob
{
    public function handle()
    {
        cache()->put('deliveredJob', $this->webhookCall);
    }
}