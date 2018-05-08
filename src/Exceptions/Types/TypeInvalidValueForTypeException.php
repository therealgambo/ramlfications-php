<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeInvalidValueForTypeException extends AbstractException
{
    public function __construct($property, $expected, $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Expected %s for property \'%s\', got (%s) "%s"', $expected, $property, gettype($value), $value
        );
        parent::__construct($message, $code, $previous);
    }
}
