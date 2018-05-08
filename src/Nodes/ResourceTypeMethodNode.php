<?php

namespace TheRealGambo\Ramlfications\Nodes;

class ResourceTypeMethodNode extends ResourceMethodNode
{
    /**
     * Whether this resourceType method definition is optional.
     * Indicated by a trailing '?' character.
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#declaring-http-methods-as-optional
     *
     * @var bool $optional
     */
    private $optional = false;

    public function __construct(array $raml, RootNode $rootNode, ResourceNode $resourceNode, string $method, bool $optional)
    {
        parent::__construct($raml, $rootNode, $resourceNode, $method);

        $this->setOptional($optional);
    }

    /**
     * Get value of Optional
     *
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * Set the value of Optional
     *
     * @param bool $optional
     *
     * @return self
     */
    public function setOptional(bool $optional): self
    {
        $this->optional = $optional;
        return $this;
    }
}