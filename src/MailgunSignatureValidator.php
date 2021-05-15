<?php


namespace Aleahy\MailgunLogger;


use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class MailgunSignatureValidator implements SignatureValidator
{

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $this->getSignature($request);

        if ($this->isOldWebhook($signature)) {
            return false;
        }

        return $this->buildSignature($signature, $config) === $signature['signature'];
    }

    protected function buildSignature($signatureArray, WebhookConfig $config) {
        return hash_hmac(
            'sha256',
            $signatureArray['timestamp'] . $signatureArray['token'],
            $config->signingSecret
        );
    }
    protected function isOldWebhook($signature):bool
    {
        return (abs(now()->timestamp - $signature['timestamp']) > 15);
    }
    protected function getSignature(Request $request): array
    {
        $validated = $request->validate([
            'signature.signature' => 'bail|required',
            'signature.timestamp' => 'required',
            'signature.token' => 'required',
        ]);
        return $validated['signature'];
    }
}
