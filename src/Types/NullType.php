<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * NullType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#null-type
 */
class NullType extends TypeNode
{

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);
    }

    public function validate($value)
    {
        if (!is_null($value)) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'null', $value);
        }
    }
}
