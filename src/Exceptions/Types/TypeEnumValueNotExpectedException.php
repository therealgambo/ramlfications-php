<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeEnumValueNotExpectedException extends AbstractException
{
    public function __construct($value, array $valid, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Expected any of [%s], got (%s) "%s"',
            implode($valid, ', '),
            gettype($value),
            $value
        );
        parent::__construct($message, $code, $previous);
    }
}
