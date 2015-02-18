<?php

namespace PulkitJalan\Google\tests;

use Mockery;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testClientGetter()
    {
        $client = Mockery::mock('PulkitJalan\Google\Client', [[]])->makePartial();
        $this->assertInstanceOf('Google_Client', $client->getClient());
    }

    public function testServiceMake()
    {
        $client = Mockery::mock('PulkitJalan\Google\Client', [[]])->makePartial();
        $this->assertInstanceOf('Google_Service_Storage', $client->make('storage'));
    }

    public function testServiceMakeException()
    {
        $client = Mockery::mock('PulkitJalan\Google\Client', [[]])->makePartial();

        $this->setExpectedException('PulkitJalan\Google\Exceptions\UnknownServiceException');

        $client->make('storag');
    }

    public function testMagicMethodException()
    {
        $client = new \PulkitJalan\Google\Client([]);

        $this->setExpectedException('BadMethodCallException');

        $client->getAuth2();
    }

    public function testAssertCredentials()
    {
        $client = new \PulkitJalan\Google\Client([
            'service' => [
                'account' => 'name',
                'scopes'  => ['scope'],
                'key'     => __DIR__.'/data/cert.p12',
            ],
        ]);

        $this->assertInstanceOf('Google_Auth_OAuth2', $client->getAuth());
    }

    public function testAppEngineCredentials()
    {
        $_SERVER['SERVER_SOFTWARE'] = 'Google App Engine';
        $client = new \PulkitJalan\Google\Client([]);

        $this->assertInstanceOf('Google_Auth_AppIdentity', $client->getAuth());

        unset($_SERVER['SERVER_SOFTWARE']);
    }

    public function testComputeEngineCredentials()
    {
        $client = new \PulkitJalan\Google\Client([]);

        $this->assertInstanceOf('Google_Auth_ComputeEngine', $client->getAuth());
    }
}
