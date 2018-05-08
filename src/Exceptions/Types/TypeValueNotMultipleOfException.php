<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeValueNotMultipleOfException extends AbstractException
{
    public function __construct($multiplier, $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Value must be a multiplier result of: %s, got %s', $multiplier, $value);
        parent::__construct($message, $code, $previous);
    }
}
