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

test('service make', function () {
    $client = new Client();

    expect($client->make('storage'))->toBeInstanceOf(Storage::class);
    expect($client->make(Storage::class))->toBeInstanceOf(Storage::class);
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
