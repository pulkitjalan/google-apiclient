<?php

namespace PulkitJalan\Google\Tests;

use BadMethodCallException;
use Google\Service\Storage;
use PulkitJalan\Google\Client;
use Google\Client as GoogleClient;
use PulkitJalan\Google\Exceptions\UnknownServiceException;

test('client getter', function () {
    $client = new Client();

    expect($client->getClient())->toBeInstanceOf(GoogleClient::class);
});

test('client getter with additional config', function () {
    $client = new Client([
        'config' => [
            'subject' => 'test',
        ],
    ]);

    expect('test')->toEqual($client->getClient()->getConfig('subject'));
});

test('client sets prompt or approval_prompt', function () {
    $client = new Client([
        'prompt' => 'auto',
        'approval_prompt' => 'consent',
    ]);

    // default value should be empty
    expect('auto')->toEqual($client->getClient()->getConfig('prompt'));

    // default value should be auto, since prompt is set, it should be ignored
    expect('auto')->toEqual($client->getClient()->getConfig('approval_prompt'));
});

test('client sets approval_prompt', function () {
    $client = new Client([
        'approval_prompt' => 'consent',
    ]);

    // default value should be empty
    expect('')->toEqual($client->getClient()->getConfig('prompt'));

    // default value should be auto
    expect('consent')->toEqual($client->getClient()->getConfig('approval_prompt'));
});

test('service make', function () {
    $client = new Client();

    expect($client->make('storage'))->toBeInstanceOf(Storage::class);
    expect($storage = $client->make(Storage::class))->toBeInstanceOf(Storage::class);
    expect($client->make($storage))->toBeInstanceOf(Storage::class);
});

test('service make exception', function () {
    $client = new Client();

    $this->expectException(UnknownServiceException::class);

    $client->make('storag');
});

test('magic method exception', function () {
    $client = new Client();

    $this->expectException(BadMethodCallException::class);

    $client->getAuthTest();
});

test('no credentials', function () {
    $client = new Client();

    expect($client->isUsingApplicationDefaultCredentials())->toBeFalse();
});

test('default credentials', function () {
    $client = new Client([
        'service' => [
            'enable' => true,
            'file' => __DIR__.'/../data/test.json',
        ],
    ]);

    expect($client->isUsingApplicationDefaultCredentials())->toBeTrue();
});
