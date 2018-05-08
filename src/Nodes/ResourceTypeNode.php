<?php

namespace TheRealGambo\Ramlfications\Nodes;

class ResourceTypeNode extends BaseNode
{
    /**
     * Resource Node properties that are not already defined in BaseNode.
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#resource-property
     */////////////////////////////////////////////////////////////////////////
//    private $displayName = '';
//    private $description = '';
//    private $annotations = [];
    private $uses = [];

    /**
     * RAML resourceType that is applied to this resource
     * @var string
     */
    private $type = '';

    /**
     * Array of methods that are applicable to this resource
     * @var array
     */
    private $methods = [];

    private $usage = '';

    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    private $name;

    public function __construct(array $raml, RootNode $rootNode, string $key)
    {
        parent::__construct($raml, $rootNode);

        $this->setName($key);

        if (isset($raml['type'])) {
            $this->setType($raml['type']);
        }

        if (isset($raml['usage'])) {
            $this->setUsage($raml['usage']);
        }

        if (isset($raml['uses'])) {
            $this->setUses($raml['uses']);
        }

        $methods = [];
        foreach ($raml as $method => $methodRaml) {
            if (in_array(strtolower($method), BaseNode::AVAILABLE_METHODS)) {
                $optional = false;

                if (substr($method, -1) === '?') {
                    $optional = true;
                    $method = substr($method, 0, -1);
                }

//                $methods[strtolower($method)] = new ResourceTypeMethodNode(
//                    $methodRaml,
//                    $rootNode,
//                    $this,
//                    strtoupper($method),
//                    $optional
//                );
            }
        }
        $this->setMethods($methods);
    }

    /**
     * Get resourceType that this resourceType inherits from
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the resourceType this resourceType inherits from
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
     * Get value of Methods available in this resourceType
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Set the value of Methods available in this resourceType
     *
     * @param array $methods
     *
     * @return self
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Get the name for this resourceType
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name for this resourceType
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
     * Get the instructions on how and when this resourceType should be used.
     *
     * @return string
     */
    public function getUsage(): string
    {
        return $this->usage;
    }

    /**
     * Set the instructions on how and when this resourceType should be used.
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

    /**
     * Get value of Uses
     *
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * Set the value of Uses
     *
     * @param array $uses
     *
     * @return self
     */
    public function setUses(array $uses)
    {
        $this->uses = $uses;
        return $this;
    }
}
