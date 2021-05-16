<?php


namespace Aleahy\MailgunLogger;


use Illuminate\Support\Facades\Http;

class MailgunService
{
    /**
     * @param string $url
     * @return array
     *
     * Gets the stored message at the provided Mailgun URL as an associative array.
     */
    public static function get(string $url): array
    {
        return Http::withBasicAuth('api', config('mailgun-logger.signing_secret'))
            ->get($url)->json();
    }

    /**
     * @param string $url
     * @return array
     *
     * Gets the stored message at the provided Mailgun URL as raw MIME.
     */
    public static function getMime(string $url): array
    {
        return Http::withBasicAuth('api', config('mailgun-logger.signing_secret'))
            ->withHeaders([
                'Accept' => 'message/rfc2822'
            ])
            ->get($url)->json();

    }
}