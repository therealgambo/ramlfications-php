<?php

namespace TheRealGambo\Ramlfications\Types;

use DateTime;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * TimeOnlyType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#time-only-type
 */
class TimeOnlyType extends TypeNode
{
    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);
    }

    public function validate($value)
    {
        parent::validate($value);

        $d = DateTime::createFromFormat('HH:II:SS', $value);

        if ($d && $d->format('HH:II:SS') !== $value) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'time format HH:II:SS', $value);
        }
    }
}
