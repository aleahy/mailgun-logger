<?php

return [
    //Put your mailgun signing secret in your env file
    'signing_secret' => env('MAILGUN_SIGNING_SECRET'),

    // If you want to process a webhook, create a job to handle the type of event.
    // Keys must be one of the event types found at
    // https://documentation.mailgun.com/en/latest/api-events.html#event-types
    'jobs' => [
//       'failed' => HandleFailedJob::class
    ],
];