<?php

namespace TheRealGambo\Ramlfications\Nodes;

/**
 * Class TraitNode
 *
 * Raml Trait Object
 *
 * @package TheRealGambo\Ramlfications\Nodes
 */
class TraitNode extends BaseNode
{
    private $name;
    private $usage = '';

    public function __construct(array $raml, RootNode $rootNode, string $key)
    {
        parent::__construct($raml, $rootNode);

        $this->setName($key);

        if (isset($raml['usage'])) {
            $this->setUsage($raml['usage']);
        }
    }

    /**
     * Get value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get value of Usage
     *
     * @return string
     */
    public function getUsage(): string
    {
        return $this->usage;
    }

    /**
     * Set the value of Usage
     *
     * @param string $usage
     *
     * @return self
     */
    public function setUsage(string $usage): self
    {
        $this->usage = $usage;
        return $this;
    }
}
