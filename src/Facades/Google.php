<?php

namespace PulkitJalan\Google\Facades;

use PulkitJalan\Google\Client;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin Client
 */
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
