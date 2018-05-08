<?php

namespace TheRealGambo\Ramlfications\Parameters;

use TheRealGambo\Ramlfications\Nodes\RootNode;

class Annotation
{
    /**
     * Annotation node properties
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#resource-property
     */////////////////////////////////////////////////////////////////////////
    /**
     * @var string
     */
    private $displayName = '';

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var array
     */
    private $allowedTargets = [];



    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    // todo: change to static class instance type? Response::class
    public static $validTargets = [
        'API',
        'DocumentationItem',
        'Resource',
        'Method',
        'Response',
        'RequestBody',
        'ResponseBody',
        'TypeDeclaration',
        'Example',
        'ResourceType',
        'Trait',
        'SecurityScheme',
        'SecuritySchemeSettings',
        'AnnotationType',
        'Library',
        'Overlay',
        'Extension'
    ];

    /**
     * @var array
     */
    private $raw = [];

    /**
     * @var string
     */
    private $key = '';

    public function __construct(array $raml, RootNode $rootNode, string $key)
    {
        $this->setRaw($raml)
             ->setKey($key);

        if (isset($raml['displayName'])) {
            $this->setDisplayName($raml['displayName']);
        } else {
            $this->setDisplayName($this->getKey());
        }

        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        if (isset($raml['allowedTargets'])) {
            foreach ($raml['allowedTargets'] as $target) {
                if (!in_array($target, array_keys(self::$validTargets))) {
                    throw new \Exception('target not allowed: ' . $target);
                }
            }
            $this->setAllowedTargets($raml['allowedTargets']);
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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
     * Get value of AllowedTargets
     *
     * @return array
     */
    public function getAllowedTargets(): array
    {
        return $this->allowedTargets;
    }

    /**
     * Set the value of AllowedTargets
     *
     * @param array $allowedTargets
     *
     * @return self
     */
    public function setAllowedTargets(array $allowedTargets): self
    {
        $this->allowedTargets = $allowedTargets;
        return $this;
    }

    /**
     * Get value of Raw
     *
     * @return array
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * Set the value of Raw
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

    /**
     * Get value of Key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the value of Key
     *
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }
}