<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Nodes\RootNode;

/**
 * IntegerType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#integer-type
 */
class IntegerType extends NumberType
{
    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (strlen($this->getFormat()) === 0) {
            $this->setFormat('int');
        }
    }
}
