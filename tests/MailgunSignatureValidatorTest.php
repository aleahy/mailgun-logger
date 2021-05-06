<?php

namespace Aleahy\MailgunLogger\Test;

use Aleahy\MailgunLogger\MailgunSignatureValidator;
use Aleahy\MailgunLogger\ProcessMailgunEventJob;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class MailgunSignatureValidatorTest extends TestCase
{
    protected $signature;
    protected $webhookConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->webhookConfig = new WebhookConfig([
            'name' => 'mailgun',
            'signing_secret' => 'abc123',
            'signature_header_name' => '',
            'signature_validator' => MailgunSignatureValidator::class,
            'webhook_profile' => ProcessEverythingWebhookProfile::class,
            'webhook_model' => WebhookCall::class,
            'process_webhook_job' => ProcessMailgunEventJob::class,
        ]);
    }

    /**
     * @test
     */
    public function test_incorrect_signature_fails()
    {
        $request = new Request();
        $request->replace([
            'signature' => [
                'signature' => '12345',
                'token' => 'abcde',
                'timestamp' => time()
            ]
        ]);
        $this->assertFalse((new MailgunSignatureValidator())->isValid($request, $this->webhookConfig));
    }

    /**
     * @test
     */
    public function test_it_accepts_a_valid_signature()
    {
        $token = '12345';
        $timestamp = now()->timestamp;
        $secret = $this->webhookConfig->signingSecret;
        $signature = $this->buildSignature($timestamp, $token, $secret);
        $request = new Request();
        $request->replace([
            'signature' => [
                'signature' => $signature,
                'token' => $token,
                'timestamp' => $timestamp
            ]
        ]);

        $this->assertTrue((new MailgunSignatureValidator())->isValid($request, $this->webhookConfig));
    }
    /**
     * @test
     */
    public function test_a_delayed_timestamp_is_rejected()
    {
        $token = '12345';
        $timestamp = now()->timestamp - 16;
        $signature = $this->buildSignature($timestamp, $token, $this->webhookConfig->signingSecret);
        $request = new Request();

        $request->replace([
            'signature' => [
                'signature' => $signature,
                'token' => $token,
                'timestamp' => $timestamp
            ]
        ]);

        $this->assertFalse((new MailgunSignatureValidator())->isValid($request, $this->webhookConfig));
    }
    
    /**
     * @test
     */
    public function test_not_setting_mailgun_secret_fails_signature_check()
    {
        $this->webhookConfig = new WebhookConfig([
            'name' => 'mailgun',
            'signing_secret' => null,
            'signature_header_name' => '',
            'signature_validator' => MailgunSignatureValidator::class,
            'webhook_profile' => ProcessEverythingWebhookProfile::class,
            'webhook_model' => WebhookCall::class,
            'process_webhook_job' => ProcessMailgunEventJob::class,
        ]);

        $token = '12345';
        $timestamp = now()->timestamp;
        $secret = 'asb123';

        $signature = $this->buildSignature($timestamp, $token, $secret);

        $request = new Request();

        $request->replace([
            'signature' => [
                'signature' => $signature,
                'token' => $token,
                'timestamp' => $timestamp
            ]
        ]);

        $this->assertFalse((new MailgunSignatureValidator())->isValid($request, $this->webhookConfig));
    }
}
