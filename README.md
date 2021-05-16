# MailgunLogger

This package allows a laravel app to receive Mailgun webhooks. 
It makes use of [Spatie's Webhook Client Package](https://github.com/spatie/laravel-webhook-client)
to capture the webhooks. You then can queue jobs to process the webhooks as you see fit.

It also has a service to retrieve stored messages through the api.

## Installation

Install the package using composer:
```
composer require aleahy/mailgun-logger
```

The service provider should autoregister.

You will need your mailgun signing secret in your `.env` file.
```
MAILGUN_SIGNING_SECRET=ABC123
```

If you want to define jobs to respond to the webhooks, you will need to publish the config file.

Publish the config file using

```
php artisan vendor:publish --provider="Aleahy\MailgunLogger\MailgunLoggerServiceProvider" --tag="config"
```

You can then define your jobs in the jobs section of the `mailgun-logger` config file.
```
/**
 * If you want to process a webhook, create a job to handle the type of event.
 * Keys must be one of the event types found at
 * https://documentation.mailgun.com/en/latest/api-events.html#event-types
 */
'jobs' => [
    'failed' => HandleFailedJob::class
],
```
Make sure the key for each job you define is one of the event types defined in the 
[Mailgun API](https://documentation.mailgun.com/en/latest/api-events.html#event-types).

You will need the migrations from the [Spatie\WebhookClient Package](https://github.com/spatie/laravel-webhook-client)
to store the webhook calls.

If you haven't already done so, publish them using:
```
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="migrations"
```
and run the migration:
```
php artisan migrate
```

## Routing

Define a route for incoming mailgun events in your `routes/web.php` file using the Route macro `mailgunWebhooks`:
```
# routes/web.php
Route::mailgunWebhooks('webhooks/mailgun');
```

This will register a `POST` route to a controller in this package.

You will also need to bypass CSRF token checking for this route by adding it
to the `VerifyCsrfToken` middleware.
```
protected $except = [
    'webhooks/mailgun',
];
```

## Usage

### Responding to Webhooks
Create a laravel job class that extends `Spatie\WebhookClient\ProcessWebhookJob` 
to be run when the webhook is received.

As the parent class receives the webhook in the constructor,
the payload can be access by `$this->webhookCall->payload`.

If you decide not to extend `Spatie\WebhookClient\ProcessWebhookJob`,
then make sure it can receive the webhook in the constructor.
It is also important that the job is queueable so that Mailgun can receive a response immediately.

An example is shown below:

```
use Spatie\WebhookClient\ProcessWebhookJob;

class HandleFailedJob extends ProcessWebhookJob
{
    public function handle()
    {
        // The payload of the webhook call with `$this->webhookCall->payload`
    }
}
```

Once the job class is created, just add it to the `jobs` array in the `mailgun-logger` config file.
The key is the event type, as listed in the Mailgun API docs, and the value is the job class name.

```
'jobs' => [
    'failed' => HandleFailedJob::class
],
```

### Getting Stored Messages
Some of the mailgun webhooks contain a url to the stored message.
You can access the contents of the message using the `MailgunService` class.

```
$message = MailgunService::get($url);
```
This will return an associative array containing various forms of the message,
under keys such as `stripped-text`, `body-html`, `stripped-html`.

You can also get the raw MIME message.
```
$mimeMessage = MailgunService::getMime($url);
```
This also returns an associative array which contains the mime data under the key `body-mime`.


## Credits
Much of this package was influenced by [binary-cats/laravel-mailgun-webhooks](https://github.com/binary-cats/laravel-mailgun-webhooks).

Thanks also to Spatie for creating such amazing packages, including their [Webhook Client Package](https://github.com/spatie/laravel-webhook-client)
which this package makes use of.
