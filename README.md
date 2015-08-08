Google Api Client Wrapper
=========

> Google api php client wrapper with Cloud Platform and Laravel support

[![Build Status](http://img.shields.io/travis/pulkitjalan/google-apiclient.svg?style=flat-square)](https://travis-ci.org/pulkitjalan/google-apiclient)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/pulkitjalan/google-apiclient/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/pulkitjalan/google-apiclient/)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/pulkitjalan/google-apiclient/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/pulkitjalan/google-apiclient/code-structure/master)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](http://www.opensource.org/licenses/MIT)
[![Latest Version](http://img.shields.io/packagist/v/pulkitjalan/google-apiclient.svg?style=flat-square)](https://packagist.org/packages/pulkitjalan/google-apiclient)
[![Total Downloads](https://img.shields.io/packagist/dt/pulkitjalan/google-apiclient.svg?style=flat-square)](https://packagist.org/packages/pulkitjalan/google-apiclient)

## Requirements

This package requires PHP >=5.4

## Installation

Install via composer - edit your `composer.json` to require the package.

```js
"require": {
    "pulkitjalan/google-apiclient": "1.*"
}
```

Then run `composer update` in your terminal to pull it in.

Or use `composer require pulkitjalan/google-apiclient`

## Laravel

To use in laravel add the following to the `providers` array in your `config/app.php`

```php
'PulkitJalan\Google\GoogleServiceProvider'
```

Next add the following to the `aliases` array in your `config/app.php`

```php
'Google' => 'PulkitJalan\Google\Facades\Google'
```

Finally run `php artisan config:publish pulkitjalan/google-apiclient` to publish the config file.

## Usage

The `Client` class takes an array as the first parameter, see example of config file below:

```php
return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => '',

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => '',
    'scopes' => [],
    'access_type' => 'online',
    'approval_prompt' => 'auto',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => '',

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the information below to use assert credentials
    | Leave blank to use app engine or compute engine.
    |
    */
    'service' => [
        /*
        | Example xxx@developer.gserviceaccount.com
        */
        'account' => '',

        /*
        | Example ['https://www.googleapis.com/auth/cloud-platform']
        */
        'scopes' => [],

        /*
        | Path to key file
        | Example storage_path().'/key/google.p12'
        */
        'key' => '',
    ]
];
```

To use the Google Cloud Platform Services you can either set the information in the config file under `service`, or if running under compute engine (or app engine) leave it blank.

`NOTE: service => ['account'] is the service Email Address and not the Client ID!`

Get `Google_Client`
```php
$client = new PulkitJalan\Google\Client($config);
$googleClient = $client->getClient();
```

Laravel Example:
```php
$googleClient = Google::getClient();
```

Get a service
```php
$client = new PulkitJalan\Google\Client($config);

// returns instance of \Google_Service_Storage
$storage = $client->make('storage');

// list buckets example
$storage->buckets->listBuckets('project id');

// get object example
$storage->objects->get('bucket', 'object');
```
