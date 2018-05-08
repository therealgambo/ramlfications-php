<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeAdditionalPropertyException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeMissingRequiredPropertyException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * ObjectType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#object-type
 */
class ObjectType extends TypeNode
{
    /**
     * The properties that instances of this type can or must have.
     *
     * @var array
     **/
    private $properties = [];

    /**
     * The minimum number of properties allowed for instances of this type.
     *
     * @var int
     **/
    private $minProperties = null;

    /**
     * The maximum number of properties allowed for instances of this type.
     *
     * @var int
     **/
    private $maxProperties = null;

    /**
     * A Boolean that indicates if an object instance has additional properties.
     * Default: true
     *
     * @var bool
     **/
    private $additionalProperties = true;

    /**
     * Determines the concrete type of an individual object at runtime when,
     * for example, payloads contain ambiguous types due to unions or inheritance.
     * The value must match the name of one of the declared properties of a type.
     *
     * Unsupported practices are inline type declarations and using discriminator with non-scalar properties.
     *
     * @var string
     **/
    protected $discriminator = null;

    /**
     * Identifies the declaring type.
     * Requires including a discriminator facet in the type declaration.
     * A valid value is an actual value that might identify the type of an
     * individual object and is unique in the hierarchy of the type.
     *
     * Inline type declarations are not supported.
     * Default: The name of the type
     *
     * @var string
     **/
    private $discriminatorValue = null;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        $this->setType('object');

        if (isset($raml['properties'])) {
            $this->setProperties($raml['properties']);
        }

        if (isset($raml['minProperties'])) {
            $this->setMinProperties($raml['minProperties']);
        }

        if (isset($raml['maxProperties'])) {
            $this->setMaxProperties($raml['maxProperties']);
        }

        if (isset($raml['additionalProperties'])) {
            $this->setAdditionalProperties($raml['additionalProperties']);
        }

        if (isset($raml['discriminator'])) {
            $this->setDiscriminator($raml['discriminator']);
        }

        if (isset($raml['discriminatorValue'])) {
            $this->setDiscriminatorValue($raml['discriminatorValue']);
        }
    }

    public function discriminate($value)
    {
        if (isset($value[$this->getDiscriminator()])) {
            if ($this->getDiscriminatorValue() !== null) {
                if ($this->getDiscriminatorValue() === $value[$this->getDiscriminator()]) {
                    return true;
                }

                return false;
            }

            return $value[$this->getDiscriminator()] === $this->getName();
        }

        return true;
    }

    /**
     * Get the value of Properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set the value of Properties
     *
     * @param array $properties
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $name => $property) {
            if (!($property instanceof TypeNode)) {
                $property = $this->getRootNode()->determineType($property, $name);
            }
            $this->properties[$name] = $property;
        }

        return $this;
    }

    /**
     * Returns a property by name
     *
     * @param string $name
     *
     * @return TypeNode|bool
     */
    public function getProperty(string $name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : false;
    }

    /**
     * Get the value of Min Properties
     *
     * @return int
     */
    public function getMinProperties(): int
    {
        return $this->minProperties;
    }

    /**
     * Set the value of Min Properties
     *
     * @param int $minProperties
     *
     * @return self
     */
    public function setMinProperties(int $minProperties): self
    {
        $this->minProperties = $minProperties;
        return $this;
    }

    /**
     * Get the value of Max Properties
     *
     * @return int
     */
    public function getMaxProperties(): int
    {
        return $this->maxProperties;
    }

    /**
     * Set the value of Max Properties
     *
     * @param int $maxProperties
     *
     * @return self
     */
    public function setMaxProperties(int $maxProperties): self
    {
        $this->maxProperties = $maxProperties;
        return $this;
    }

    /**
     * Get the value of Additional Properties
     *
     * @return bool
     */
    public function getAdditionalProperties(): bool
    {
        return $this->additionalProperties;
    }

    /**
     * Set the value of Additional Properties
     *
     * @param bool $additionalProperties
     *
     * @return self
     */
    public function setAdditionalProperties(bool $additionalProperties): self
    {
        $this->additionalProperties = $additionalProperties;
        return $this;
    }

    /**
     * Get the value of Discriminator
     *
     * @return mixed
     */
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    /**
     * Set the value of Discriminator
     *
     * @param mixed $discriminator
     *
     * @return self
     */
    public function setDiscriminator($discriminator)
    {
        $this->discriminator = $discriminator;
        return $this;
    }

    /**
     * Get the value of Discriminator Value
     *
     * @return mixed
     */
    public function getDiscriminatorValue()
    {
        return $this->discriminatorValue;
    }

    /**
     * Set the value of Discriminator Value
     *
     * @param mixed $discriminatorValue
     *
     * @return self
     */
    public function setDiscriminatorValue($discriminatorValue)
    {
        $this->discriminatorValue = $discriminatorValue;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        // an object is in essence just a group (array) of datatypes
        if (!is_array($value)) {
            if (!is_object($value)) {
                throw new TypeInvalidValueForTypeException($this->getName(), 'object', $value);
            }
            // in case of stdClass - convert it to array for convenience
            $value = get_object_vars($value);
        }

        foreach ($this->getProperties() as $property) {
            /** @var TypeNode $property */
            if ($property->getRequired() && !array_key_exists($property->getName(), $value)) {
                throw new TypeMissingRequiredPropertyException($property->getName());
            }
        }

        foreach ($value as $name => $propertyValue) {
            $property = $this->getProperty($name);
            if ($property === false) {
                if ($this->getAdditionalProperties() === false) {
                    throw new TypeAdditionalPropertyException($name);
                }

                continue;
            }

            $property->validate($propertyValue);
        }
    }
}
