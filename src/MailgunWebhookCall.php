<?php


namespace Aleahy\MailgunLogger;


class MailgunWebhookCall extends \Spatie\WebhookClient\Models\WebhookCall
{
    protected $table = 'webhook_calls';

    public function getEventAttribute()
    {
        return $this->payload['event-data']['event'];
    }
}