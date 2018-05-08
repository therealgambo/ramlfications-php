<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Nodes\RootNode;

/**
 * LazyProxyType class for lazy loading datatype objects
 */
class LazyProxyType
{
    /**
     * name/id of type
     *
     * @var string
     **/
    private $name;

    /**
     * original type name, used for resolving
     *
     * @var string
     **/
    private $type;

    /**
     * original type
     *
     * @var \Raml\TypeInterface
     **/
    private $wrappedObject = null;

    /**
     * raml definition
     *
     * @var array
     **/
    private $definition = [];

    private $errors = [];

    private $rootNode;

    public function __construct(array $raml, RootNode $rootNode, string $key)
    {
        $this->name = $key;
        $this->definition = $raml;
        $this->rootNode = $rootNode;

        if (!isset($raml['type'])) {
            throw new \Exception('Missing "type" key in $raml param to determine datatype!');
        }

        $this->type = $raml['type'];
    }

    /**
     * Dumps object to array
     *
     * @return array Object dumped to array.
     */
    public function toArray()
    {
        return $this->definition;
    }

    /**
     * Returns type definition
     *
     * @return array Definition of object.
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Get the value of name
     *
     * @return string Returns name property.
     */
    public function getName()
    {
        return $this->name;
    }

    public function discriminate($value)
    {
        if (!$this->getWrappedObject()->discriminate($value)) {
            if (isset($value[$this->getDiscriminator()])) {
                $discriminatorValue = $this->getDiscriminatorValue() ?: $this->getName();

                return $value[$this->getDiscriminator()] === $discriminatorValue;
            }

            return true;
        }

        return true;
    }

    /**
     * Magic method to proxy all method calls to original object
     * @param string    $name       Name of called method.
     * @param mixed     $params     Parameteres of called method.
     *
     * @return mixed Returns whatever the actual method returns.
     */
    public function __call($name, $params)
    {
        $original = $this->getResolvedObject();

        return call_user_func_array(array($original, $name), $params);
    }

    public function getRequired()
    {
        if (isset($this->definition['required'])) {
            return $this->definition['required'];
        }

        return $this->getResolvedObject()->getRequired();
    }

    public function validate($value)
    {
        $original = $this->getResolvedObject();

        if ($this->discriminate($value)) {
            $original->validate($value);
        }
    }

    /**
     * @return TypeValidationError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    public function getResolvedObject()
    {
        $object = $this->getWrappedObject();
        if ($object instanceof self) {
            $definition = $object->getDefinitionRecursive();
            return $this->rootNode->determineType($definition, $this->name);
        }

        return $object;
    }

    public function getWrappedObject()
    {
        if (is_null($this->wrappedObject)) {
            $this->wrappedObject = $this->rootNode->getType($this->type);
        }

        return $this->wrappedObject;
    }

    public function getDefinitionRecursive()
    {
        $type = $this->getWrappedObject();
        $typeDefinition = ($type instanceof self) ? $type->getDefinitionRecursive() : $type->getDefinition();
        $recursiveDefinition = array_replace_recursive($typeDefinition, $this->getDefinition());
        $recursiveDefinition['type'] = $typeDefinition['type'];

        return $recursiveDefinition;
    }
}
