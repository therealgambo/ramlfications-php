<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeNotStringException extends AbstractException
{
    public function __construct($value, $code = 0, Throwable $previous = null)
    {
        if (is_array($value)) {
            $message = sprintf('Expected string, got (%s) "%s"', gettype($value), print_r($value, true));
        } else {
            $message = sprintf('Expected string, got (%s) "%s"', gettype($value), $value);
        }
        parent::__construct($message, $code, $previous);
    }
}