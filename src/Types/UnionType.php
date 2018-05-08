<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;
use TheRealGambo\Ramlfications\Utilities\TypeValidationError;

/**
 * UnionType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#union-type
 */
class UnionType extends TypeNode
{
    /**
     * Possible Types
     *
     * @var array
     **/
    private $possibleTypes = [];

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        $this->setPossibleTypes(explode('|', $this->getType()));
        $this->setType('union');
    }

    /**
     * Get the value of Possible Types
     *
     * @return array
     */
    public function getPossibleTypes(): array
    {
        return $this->possibleTypes;
    }

    /**
     * Set the value of Possible Types
     *
     * @param array $possibleTypes
     *
     * @return self
     */
    public function setPossibleTypes(array $possibleTypes)
    {
        foreach ($possibleTypes as $type) {
            $this->possibleTypes[] = $this->getRootNode()->determineType(
                ['type' => trim($type)],
                trim($type)
            );
        }

        return $this;
    }

    public function validate($value)
    {
        foreach ($this->getPossibleTypes() as $type) {
            /** @var TypeNode $type */
            if (!$type->discriminate($value)) {
                continue;
            }

            $type->validate($value);
        }

        $this->errors[] = TypeValidationError::unionTypeValidationFailed($this->getName(), $errors);
    }
}
