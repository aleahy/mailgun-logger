<?php


namespace Aleahy\MailgunLogger;


use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class MailgunLoggerWebhookController
{
    public function __invoke(Request $request) {
        //Create Spatie config
        $webhookConfig = new WebhookConfig([
            'name' => 'mailgun',
            'signing_secret' => config('mailgun-logger.signing_secret'),
            'signature_header_name' => '',
            'signature_validator' => MailgunSignatureValidator::class,
            'webhook_profile' => ProcessEverythingWebhookProfile::class,
            'webhook_model' => WebhookCall::class,
            'process_webhook_job' => ProcessMailgunEventJob::class,
        ]);
        //Process the request
        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

}