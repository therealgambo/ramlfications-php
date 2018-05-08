<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArraySizeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArrayValueTypeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueNotUniqueException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * ArrayType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#array-type
 */
class ArrayType extends TypeNode
{

    /**
     * Scalar types which we can validate
     */
    private static $SCALAR_TYPES = [
        'integer',
        'string',
        'boolean',
        'number',
    ];

    /**
     * Boolean value that indicates if items in the array MUST be unique.
     *
     * @var bool
     **/
    private $uniqueItems = false;

    /**
     * Indicates the type all items in the array are inherited from. Can be a reference to an
     * existing type or an inline type declaration.
     *
     * @var TypeNode
     **/
    private $itemType;

    /**
     * Minimum amount of items in array. Value MUST be equal to or greater than 0.
     * Default: 0.
     *
     * @var int
     **/
    private $minItems = 0;

    /**
     * Maximum amount of items in array. Value MUST be equal to or greater than 0.
     * Default: 2147483647.
     *
     * @var int
     **/
    private $maxItems = 2147483647;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (isset($raml['uniqueItems'])) {
            $this->setUniqueItems($raml['uniqueItems']);
        }

        if (isset($raml['items'])) {
            $this->setItemType($raml['items']);
        }

        if (isset($raml['minItems'])) {
            $this->setMinItems($raml['minItems']);
        }

        if (isset($raml['maxItems'])) {
            $this->setMaxItems($raml['maxItems']);
        }

        $pos = strpos($this->getType(), '[]');
        if ($pos !== false) {
            $this->setItemType(substr($this->getType(), 0, $pos));
        }

        $this->setType('array');
    }

    public function getUniqueItems(): bool
    {
        return $this->uniqueItems;
    }

    /**
     * @param bool $uniqueItems
     *
     * @return self
     */
    public function setUniqueItems(bool $uniqueItems): self
    {
        $this->uniqueItems = $uniqueItems;
        return $this;
    }

    /**
     * @param string $itemType
     *
     * @return self
     */
    public function setItemType(string $itemType): self
    {
        $this->itemType = $itemType;
        return $this;
    }

    /**
     * Get the value of Items
     *
     * @return string
     */
    public function getItemType(): string
    {
        return $this->itemType;
    }

    public function getMinItems(): int
    {
        return $this->minItems;
    }

    /**
     * @param int $minItems
     *
     * @return self
     */
    public function setMinItems(int $minItems): self
    {
        $this->minItems = $minItems;
        return $this;
    }

    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     *
     * @return self
     */
    public function setMaxItems(int $maxItems): self
    {
        $this->maxItems = $maxItems;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        if (!is_array($value)) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'is array', $value);
        }

        $actualArraySize = count($value);
        if (!($actualArraySize >= $this->getMinItems() && $actualArraySize <= $this->getMaxItems())) {
            throw new TypeInvalidArraySizeException(
                $this->getName(),
                $this->getMinItems(),
                $this->getMaxItems(),
                $actualArraySize
            );
        }

        //todo: exception when list of arrays provided, but single array expected

        if ($this->getUniqueItems() === true && $actualArraySize !== count($this->arrayUnique($value))) {
            throw new TypeValueNotUniqueException($this->getName());
        }

        if (in_array($this->getItemType(), self::$SCALAR_TYPES)) {
            $this->validateScalars($value);
        } else {
            $this->validateObjects($value);
        }
    }

    private function validateScalars($value)
    {
        foreach ($value as $valueItem) {
            switch ($this->getItemType()) {
                case 'integer':
                    if (!is_int($valueItem)) {
                        throw new TypeInvalidArrayValueTypeException($this->getName(), 'integer', $valueItem);
                    }
                    break;
                case 'string':
                    if (!is_string($valueItem)) {
                        throw new TypeInvalidArrayValueTypeException($this->getName(), 'string', $valueItem);
                    }
                    break;
                case 'boolean':
                    if (!is_bool($valueItem)) {
                        throw new TypeInvalidArrayValueTypeException($this->getName(), 'boolean', $valueItem);
                    }
                    break;
                case 'number':
                    if (!is_float($valueItem) && !is_int($valueItem)) {
                        throw new TypeInvalidArrayValueTypeException($this->getName(), 'number', $valueItem);
                    }
                    break;
            }
        }
    }

    private function validateObjects(array $value)
    {
        $itemType = $this->getRootNode()->getType($this->getItemType());
        foreach ($value as $valueItem) {
            if ($itemType instanceof TypeInterface) {
                $itemType->validate($valueItem);
            }
        }
    }

    private function arrayUnique(array $array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->arrayUnique($value);
            }
        }

        return $result;
    }
}
