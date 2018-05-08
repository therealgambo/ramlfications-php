<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeAdditionalPropertyException extends AbstractException
{
    public function __construct($value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Additional property found: "%s"', $value);
        parent::__construct($message, $code, $previous);
    }
}
