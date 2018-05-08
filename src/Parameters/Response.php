<?php

namespace TheRealGambo\Ramlfications\Parameters;

use TheRealGambo\Ramlfications\Nodes\RootNode;

class Response implements InheritTypePropertiesInterface
{
    private $namedParams = [
        "description", "type", "enum", "pattern", "minimum", "maximum", "example",
        "default", "required", "repeat", "displayName", "maxLength",
        "minLength"
    ];

    private $rootNode;

    /**
     * The response code for this response
     *
     * @var integer|string $code
     */
    private $code;

    private $raw = [];

    private $description = '';

    private $headers = [];

    private $body = [];

    private $annotations = [];

    public function __construct(array $raml, RootNode $rootNode, int $code)
    {
        if (is_null($raml)) {
            $raml = [];
        }

        $this->setRaw($raml)
             ->setCode($code)
             ->setRootNode($rootNode);

        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        if (isset($raml['headers'])) {
            $headers = [];
            foreach ($raml['headers'] as $key => $headerRaml) {
                if (is_string($headerRaml)) {
                    $headerRaml = ['type' => $headerRaml];
                }
                $headers[$key] = new Header($headerRaml, $key);
            }

            $this->setHeaders($headers);
        }

        if (isset($raml['body'])) {
            $bodies = [];

            if (is_array($raml['body'])) {
                foreach ($raml['body'] as $mimeType => $ramlBody) {
                    // @todo: verify regex conforms to RFC6838
                    if (preg_match('/application\/[A-Za-z\.\-0-9]?(json|xml)/', $mimeType)) {
                        $ramlBody = is_null($ramlBody) ? [] : $ramlBody;
                        $bodies[$mimeType] = new Body($ramlBody, $rootNode, $mimeType);
                    }
                }
            }

            foreach ($rootNode->getMediaType() as $mimeType) {
                if (!array_key_exists($mimeType, $bodies)) {
                    $bodies[$mimeType] = new Body($raml['body'], $rootNode, $mimeType);
                }
            }

            $this->setBody($bodies);
        }
    }

    public function inheritTypeProperties($inheritedProperty)
    {
//        for param in inherited_param:
//            for n in NAMED_PARAMS:
//                attr = getattr(self, n, None)
//                if attr is None:
//                    attr = getattr(param, n, None)
//                    setattr(self, n, attr)

        foreach ($inheritedProperty as $param) {
            foreach ($this->namedParams as $namedParam) {
                if (property_exists($this, $namedParam) && is_null($this->$namedParam)) {
                    $reflection = new \ReflectionClass($param);
                    $property = $reflection->getProperty($namedParam);
                    $property->setAccessible(true);

                    $function = 'set' . ucfirst($namedParam);
                    $this->$function($property->getValue());
                }
            }
        }
    }

    /**
     * Get value of Code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Set the value of Code
     *
     * @param int $code
     *
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
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
     * Get value of Headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific response header
     *
     * @param string $key
     *
     * @return bool
     */
    public function getHeader(string $key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : false;
    }

    /**
     * Set the value of Headers
     *
     * @param array $headers
     *
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get all response bodies
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get a response body by mime-type
     *
     * @param string $type
     *
     * @return Body|bool
     */
    public function getBodyByType(string $type)
    {
        return isset($this->body[$type]) ? $this->body[$type] : false;
    }

    /**
     * Set the value of Body
     *
     * @param array $body
     *
     * @return self
     */
    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
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
}
