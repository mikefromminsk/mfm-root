<?php
use \Controller;

$client = OpenAI::client("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");

$result = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!'],
    ],
]);