<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeMissingRequiredPropertyException extends AbstractException
{
    public function __construct($value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Missing required property: "%s"', $value);
        parent::__construct($message, $code, $previous);
    }
}