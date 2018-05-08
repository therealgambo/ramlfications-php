<?php

namespace TheRealGambo\Ramlfications\Nodes;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeEnumValueNotExpectedException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeMissingRequiredPropertyException;
use TheRealGambo\Ramlfications\Types\ObjectType;
use TheRealGambo\Ramlfications\Types\TypeInterface;
use TheRealGambo\Ramlfications\Utilities\TypeValidationError;

/**
 * Type class
 *
 * @author Melvin Loos <m.loos@infopact.nl>
 */
class TypeNode implements TypeInterface
{
    const VALID_TYPES = [
        'time-only', 'datetime', 'datetime-only', 'date-only', 'number', 'integer',
        'boolean', 'string', 'null', 'nil', 'file', 'array', 'object'
    ];

    private $raw = [];

    private $rootNode;

    /**
     * @var TypeValidationError[]
     */
    protected $errors = [];

    /**
     * Parent object
     *
     * @var TypeNode|string|null
     **/
    private $parent = null;

    /**
     * Key used for type
     *
     * @var string
     **/
    private $name = '';

    /**
     * Type
     *
     * @var string
     **/
    private $type = 'string';

    /**
     * Required
     *
     * @var bool
     **/
    private $required = true;

    /**
     * Raml definition
     *
     * @var array
     **/
    private $definition = [];

    /**
     * @var array
     */
    private $enum = [];

    /**
     *  Create new type
     *
     * @param array    $raml
     * @param RootNode $rootNode
     * @param string   $name
     */
    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        $this->setName($name)
             ->setRootNode($rootNode);

        $this->raw = $raml;

        if (isset($raml['type'])) {
            $this->setType($raml['type']);
        }

        if (isset($raml['usage'])) {
            $this->setUsage($raml['usage']);
        }

        if (isset($raml['required'])) {
            $this->setRequired($raml['required']);
        }

        if (isset($raml['enum'])) {
            $this->setEnum($raml['enum']);
        }

        if (substr($name, -1) === '?') {
            $this->setRequired(false);
            $this->setName(substr($name, 0, -1));
        }

        $this->setDefinition($raml);
    }

    /**
     * Get value of RootNode
     *
     * @return RootNode
     */
    public function getRootNode(): RootNode
    {
        return $this->rootNode;
    }

    /**
     * Set the value of RootNode
     *
     * @param RootNode $rootNode
     *
     * @return self
     */
    public function setRootNode(RootNode $rootNode): self
    {
        $this->rootNode = $rootNode;
        return $this;
    }

    public function discriminate($value)
    {
        return true;
    }

    /**
     * Dumps object to array
     *
     * @return array Object dumped to array.
     */
    public function toArray(): array
    {
        return $this->definition;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set definition
     *
     * @param array $data Definition data of type.
     *
     * @return self
     **/
    public function setDefinition(array $data = []): self
    {
        $this->definition = $data;
        return $this;
    }

    /**
     * Get definition
     *
     * @return array Returns definition property.
     */
    public function getDefinition(): array
    {
        return $this->definition;
    }

    /**
     * Get the value of Required
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the value of Required
     *
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param array $enum
     *
     * @return self
     */
    public function setEnum(array $enum): self
    {
        $this->enum = $enum;
        return $this;
    }

    /**
     * Get the value of Parent
     *
     * @return TypeNode
     */
    public function getParent()
    {
        if (is_string($this->parent) && !in_array($this->parent, self::VALID_TYPES)) {
            $parent = $this->getRootNode()->getType($this->parent);

            if ($parent === false) {
                throw new \Exception('Error getting type parent: ' . $this->parent);
            }

            $this->parent = $parent;
        }

        return $this->parent;
    }

    /**
     * Returns true when parent property is set
     *
     * @return bool Returns true when parent exists, false if not.
     */
    public function hasParent(): bool
    {
        return !is_null($this->parent);
    }

    /**
     * Set the value of Parent
     *
     * @param ObjectType|string $parent
     *
     * @return self
     */
    public function setParent($parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Inherit properties from parent (recursively)
     *
     * @return self
     **/
    public function inheritFromParent()
    {
        if (!$this->hasParent()) {
            return $this;
        }

        $parent = $this->getParent();

        // recurse if
        if ($parent instanceof $this && $parent->hasParent()) {
            $this->parent = $parent->inheritFromParent();
            unset($parent);
        }

        if ($this->getType() === 'reference') {
            return $this->getParent();
        }

        if (!($this->getParent() instanceof $this)) {
            throw new \Exception(sprintf(
                'Inheritance not possible because of incompatible Types, child is instance of %s ' .
                'and parent is instance of %s. Child Type: %s:%s Parent Type: %s:%s',
                get_class($this),
                get_class($this->getParent()),
                $this->getName(),
                $this->getType(),
                $this->getParent()->getName(),
                $this->getParent()->getType()
            ));
        }

        // retrieve all getter/setters so we can check all properties for possible inheritance
        $getters = [];
        $setters = [];
        foreach (get_class_methods($this) as $method) {
            $result = preg_split('/^(get|set)(.*)$/', $method, null, PREG_SPLIT_NO_EMPTY);
            if (count($result) === 2) {
                if ($result[0] === 'get') {
                    $getters[lcfirst($result[1])] = $method;
                }
                if ($result[0] === 'set') {
                    $setters[lcfirst($result[1])] = $method;
                }
            }
        }

        $properties = array_keys(array_merge($getters, $setters));
        foreach ($properties as $prop) {
            if (!isset($getters[$prop]) || !isset($setters[$prop])) {
                continue;
            }

            $getter = $getters[$prop];
            $setter = $setters[$prop];
            $currentValue = $this->$getter();
            // if it is unset, make sure it is equal to parent
            if ($currentValue === null) {
                $this->$setter($this->getParent()->$getter());
            }
            // if it is an array, add parent values
            if (is_array($currentValue)) {
                $newValue = array_merge($this->getParent()->$getter(), $currentValue);
                $this->$setter($newValue);
                continue;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        $this->errors = [];
        if ($this->required && !isset($value)) {
            throw new TypeMissingRequiredPropertyException($this->getName());
//            $this->errors[] = new TypeValidationError($this->getName(), 'required');
        }

        if ($this->getEnum() && !in_array($value, $this->getEnum(), true)) {
            throw new TypeEnumValueNotExpectedException($this->getName(), $this->getEnum());
//            $this->errors[] = TypeValidationError::unexpectedValue($this->getName(), $this->getEnum(), $value);
        }
    }
}
