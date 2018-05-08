<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeValueNotUniqueException extends AbstractException
{
    public function __construct($property, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Values must be unique for type property: %s', $property);
        parent::__construct($message, $code, $previous);
    }
}
