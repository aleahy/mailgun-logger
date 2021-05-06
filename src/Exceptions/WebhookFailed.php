<?php


namespace Aleahy\MailgunLogger\Exceptions;

use Exception;
use Spatie\WebhookClient\Models\WebhookCall;

class WebhookFailed extends Exception
{
    public static function missingEvent(WebhookCall $webhookCall)
    {
        return new static("Webhook call id `{$webhookCall->id}` did not contain an event.");
    }

    public static function jobClassDoesNotExist(string $jobClass, WebhookCall $webhookCall)
    {
        return new static("Could not process webhook call id `{$webhookCall->id}` because configured job class `{$jobClass}` does not exist.");
    }
}