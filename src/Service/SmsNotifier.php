<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsNotifier
{
    private Client $client;

    public function __construct(
        private readonly string $from,
        string $sid,
        string $token,
    ) {
        $this->client = new Client($sid, $token);
    }

    public function send(string $to, string $message): void
    {
        $message = $this->client->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
}
