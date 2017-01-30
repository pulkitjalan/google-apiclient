<?php

namespace PulkitJalan\Google\Facades;

use Illuminate\Support\Facades\Facade;
use PulkitJalan\Google\Client;

class Google extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}
