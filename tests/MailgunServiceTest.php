<?php


namespace Aleahy\MailgunLogger\Test;


use Aleahy\MailgunLogger\MailgunService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MailgunServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'https://se.api.mailgun.net/*' => Http::response($this->getFileStub('EmailContents.txt'), 200),
        ]);
        config(['mailgun-logger.signing_secret' => 'abc123']);
    }

    /**
     * @test
     */
    public function test_sends_request_to_get_email_contents_with_correct_attributes()
    {
        $this->withoutExceptionHandling();
        $url = 'https://se.api.mailgun.net/v3/domains/mail.languagetub.com/messages/message_key';
        MailgunService::get($url);
        Http::assertSent(function (Request $request) use ($url){
            return Str::before($request->url(), '?') === $url &&
                $request->hasHeader('Authorization');
        });

    }
}