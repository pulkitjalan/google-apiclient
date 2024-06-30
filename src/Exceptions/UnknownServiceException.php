<?php

namespace PulkitJalan\Google\Exceptions;

use Throwable;

class UnknownServiceException extends \Exception
{
    public static function throwForService(string $service, int $code = 0, Throwable|null $previous = null)
    {
        throw new static("Unknown service [$service].", $code, $previous);
    }
}
