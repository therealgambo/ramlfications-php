<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeValueExceedsMinimumException extends AbstractException
{
    public function __construct($minimum, $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Minimum allowed value/length: %s, got %s', $minimum, $value);
        parent::__construct($message, $code, $previous);
    }
}
