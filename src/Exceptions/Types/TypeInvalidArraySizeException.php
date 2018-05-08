<?php

namespace TheRealGambo\Ramlfications\Exceptions\Types;

use TheRealGambo\Ramlfications\Exceptions\AbstractException;
use Throwable;

class TypeInvalidArraySizeException extends AbstractException
{
    public function __construct(string $property, int $min, int $max, int $value, $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Allowed array size for property \'%s\' is between %s and %s, got %s', $property, $min, $max, $value
        );
        parent::__construct($message, $code, $previous);
    }
}
