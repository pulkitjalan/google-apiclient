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

test('client setter', function () {
    $client = new Client();

    $client = $client->setClient($googleClient = new GoogleClient());

    expect($client->getClient())->toBe($googleClient);
});

test('client getter with additional config', function () {
    $client = new Client([
        'config' => [
            'subject' => 'test',
        ],
    ]);

    expect($client->getClient()->getConfig('subject'))->toEqual('test');
});

test('client fallback to use application default credentials', function () {
    $client = new Client([
        'service' => [
            'enable' => true,
        ],
    ]);

    expect($client->getClient()->isUsingApplicationDefaultCredentials())->toBeTrue();
});

test('client sets prompt or approval_prompt', function () {
    $client = new Client([
        'prompt' => 'auto',
        'approval_prompt' => 'consent',
    ]);

    // default value should be empty
    expect($client->getClient()->getConfig('prompt'))->toEqual('auto');

    // default value should be auto, since prompt is set, it should be ignored
    expect($client->getClient()->getConfig('approval_prompt'))->toEqual('auto');
});

test('client sets approval_prompt', function () {
    $client = new Client([
        'approval_prompt' => 'consent',
    ]);

    // default value should be empty
    expect($client->getClient()->getConfig('prompt'))->toBeEmpty();

    // default value should be auto
    expect($client->getClient()->getConfig('approval_prompt'))->toEqual('consent');
});

test('service make', function () {
    $client = new Client();

    expect($client->make('storage'))->toBeInstanceOf(Storage::class);
    expect($storage = $client->make(Storage::class))->toBeInstanceOf(Storage::class);
    expect($client->make($storage))->toBeInstanceOf(Storage::class);
});

test('service make exception', function () {
    $client = new Client();

    $client->make('storag');
})->throws(UnknownServiceException::class);

test('magic method exception', function () {
    $client = new Client();

    $client->getAuthTest();
})->throws(BadMethodCallException::class);

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
