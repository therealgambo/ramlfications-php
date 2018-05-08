<?php

namespace TheRealGambo\Ramlfications\Nodes;

use TheRealGambo\Ramlfications\Parameters\Content;

/**
 * Class BaseNode
 *
 * @package TheRealGambo\Ramlfications\Nodes
 */
class BaseNode
{
    const METHOD_PROPERTIES = [
        "headers", "body", "responses", "queryParameters"
    ];

    const AVAILABLE_METHODS = [
        "get", "post", "put", "delete", "patch", "head", "options",
        "trace", "connect", "get?", "post?", "put?", "delete?", "patch?", "head?", "options?",
        "trace?", "connect?"
    ];

    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    private $raw = [];
    private $rootNode = null;

    /**
     * Common node properties
     *
     * @see https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#resource-property
     * @see https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#methods
     */////////////////////////////////////////////////////////////////////////
    protected $displayName = '';
    private $description = '';
    private $annotations = [];
    private $securedBy = [];
    private $is = [];


    /**
     * BaseNode constructor.
     *
     * @param array    $raml
     * @param RootNode $rootNode
     */
    public function __construct(array $raml, RootNode $rootNode)
    {
        $this->setRaw($raml)
             ->setRootNode($rootNode);

        if (isset($raml['displayName'])) {
            $this->setDisplayName($raml['displayName']);
        }

        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        // todo: annotations
        $annotations = [];
        foreach ($raml as $key => $annotation) {
            if (substr($key, 0, 1) === '(' && substr($key, -1) === ')') {
                $annotations[] = '';
            }
        }
        $this->setAnnotations($annotations);

        if (isset($raml['securedBy'])) {
            $this->setSecuredBy($raml['securedBy']);
        }

        if (isset($raml['is'])) {
            $this->setIs($raml['is']);
        }
    }

    /**
     * Get value of DisplayName
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Set the value of DisplayName
     *
     * @param string $displayName
     *
     * @return self
     */
    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get value of Description
     *
     * @return Content
     */
    public function getDescription(): Content
    {
        return new Content($this->description);
    }

    /**
     * Set the value of Description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get value of Annotations
     *
     * @return array
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * Check if a specific annotation applies to this node
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAnnotation(string $key): bool
    {
        return isset($this->annotations[$key]);
    }

    /**
     * Get a specific annotation applied to this node
     *
     * @param string $key
     *
     * @return bool|mixed
     */
    public function getAnnotation(string $key)
    {
        return isset($this->annotations[$key]) ? $this->annotations[$key] : false;
    }

    /**
     * Set the value of Annotations
     *
     * @param array $annotations
     *
     * @return self
     */
    public function setAnnotations(array $annotations): self
    {
        $this->annotations = $annotations;
        return $this;
    }

    /**
     * Get value of SecuredBy
     *
     * @return array
     */
    public function getSecuredBy(): array
    {
        return $this->securedBy;
    }

    /**
     * Check if this node is secured by a securityScheme
     *
     * @param string $key
     *
     * @return bool
     */
    public function isSecuredBy(string $key): bool
    {
        return isset($this->securedBy[$key]);
    }

    /**
     * Set the value of SecuredBy
     *
     * @param array $securedBy
     *
     * @return self
     */
    public function setSecuredBy(array $securedBy): self
    {
        array_walk($securedBy, function(&$item) {
            if (is_null($item)) {
                $item = 'null';
            }
        });

        $this->securedBy = $securedBy;
        return $this;
    }

    /**
     * Get value of Is
     *
     * @return array
     */
    public function getIs(): array
    {
        return $this->is;
    }

    /**
     * Set the value of Is
     *
     * @param array $is
     *
     * @return self
     */
    public function setIs(array $is): self
    {
        $this->is = $is;
        return $this;
    }


    /**
     * Get the RootNode
     *
     * @return RootNode
     */
    public function getRootNode(): RootNode
    {
        return $this->rootNode;
    }

    /**
     * Set the RootNode
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

    /**
     * Return raw RAML array
     *
     * @return array
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * Get a raw RAML element by key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getRawElement(string $key)
    {
        return isset($this->raw[$key]) ? $this->raw[$key] : null;
    }

    /**
     * Set the raw RAML array
     *
     * @param array $raw
     *
     * @return self
     */
    public function setRaw(array $raw): self
    {
        $this->raw = $raw;
        return $this;
    }
}
