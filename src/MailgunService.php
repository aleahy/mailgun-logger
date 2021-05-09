<?php


namespace Aleahy\MailgunLogger;


use Illuminate\Support\Facades\Http;

class MailgunService
{
    public static function get(string $url)
    {
        return Http::withBasicAuth('api', config('mailgun-logger.signing_secret'))
            ->get($url)->json();

        return json_decode($response->body(), true);
    }
}