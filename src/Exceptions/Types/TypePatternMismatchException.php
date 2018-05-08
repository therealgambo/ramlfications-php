<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypePatternMismatchException extends AbstractException
{
    public function __construct($pattern, $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('String "%s" did not match pattern /%s/', $value, $pattern);
        parent::__construct($message, $code, $previous);
    }
}