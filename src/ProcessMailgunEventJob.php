<?php


namespace Aleahy\MailgunLogger;

use Aleahy\MailgunLogger\Exceptions\WebhookFailed;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessMailgunEventJob extends ProcessWebhookJob
{
    public function handle()
    {
        if (!isset($this->webhookCall->payload['event-data']['event']) || $this->webhookCall->payload['event-data']['event'] === '') {
            dd(get_class($this->webhookCall));
            throw WebhookFailed::missingEvent($this->webhookCall);
        }

        $jobClass = $this->determineJobClass($this->webhookCall->payload['event-data']['event']);
        if ($jobClass === '') {
            return;
        }

        if (!class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $this->webhookCall);
        }

        dispatch(new $jobClass($this->webhookCall));
    }

    public function determineJobClass(string $eventType): string
    {
        return config('mailgun-logger.jobs.' . $eventType, '');
    }
}
