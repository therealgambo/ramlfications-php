<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeInvalidArrayValueTypeException extends AbstractException
{
    public function __construct($property, $expected, $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Expected array element type %s, got (%s) "%s"',
            $property,
            gettype($value),
            $value
        );
        parent::__construct($message, $code, $previous);
    }
}
