<?php

namespace TheRealGambo\Ramlfications\Nodes;

use TheRealGambo\Ramlfications\Exceptions\MutuallyExclusiveException;
use TheRealGambo\Ramlfications\Parameters\Body;
use TheRealGambo\Ramlfications\Parameters\Header;
use TheRealGambo\Ramlfications\Parameters\QueryParameter;
use TheRealGambo\Ramlfications\Parameters\Response;

class ResourceMethodNode extends BaseNode
{
    /**
     * Resource Method Node properties that are not already defined in BaseNode.
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#methods
     */////////////////////////////////////////////////////////////////////////
//    private $displayName = '';
//    private $description = '';
//    private $annotations = [];

    /**
     * Array of queryParameters that are applicable to this resource
     * @var array
     */
    private $queryParameters = [];

    /**
     * Array of headers that are applicable to this resource
     * @var array
     */
    private $headers = [];

    /**
     * todo: array or object? it is a raml array
     * https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#query-strings-and-query-parameters
     * @var array
     */
    private $queryString = [];

    /**
     * Array of responses that this resource provides
     * @var array
     */
    private $responses = [];

    /**
     * Resource request body payload
     * @var null
     */
    private $body = null;

    /**
     * Array of protocols applicable to this resource
     * @var array
     */
    private $protocols = [];

//    private $is = [];
//    private $securedBy;

    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    /**
     * Reference of resource node for which this method node is for.
     * @var ResourceNode
     */
    private $resource;

    private $method = '';

    /**
     * ResourceMethodNode constructor.
     *
     * @param array        $raml
     * @param RootNode     $rootNode
     * @param ResourceNode $resourceNode
     *
     * @throws MutuallyExclusiveException
     */
    public function __construct(array $raml, RootNode $rootNode, ResourceNode $resourceNode, string $method)
    {
        parent::__construct($raml, $rootNode);

        $this->setResource($resourceNode);
        $this->method = $method;

        // queryParameters and queryString are mutually exclusive!
        if (isset($raml['queryParameters']) && isset($raml['queryString'])) {
            throw new MutuallyExclusiveException(
                'The properties \'queryParameters\' and \'queryString\' must be mutually exclusive.'
            );
        }

        // Check and set queryParameters node
        if (isset($raml['queryParameters'])) {
            $queryParameters = [];
            foreach ($raml['queryParameters'] as $key => $queryParameterRaml) {
                $queryParameters[$key] = new QueryParameter($queryParameterRaml, $key);
            }

            $this->setQueryParameters($queryParameters);
        }

        // Check and set headers node
        if (isset($raml['headers'])) {
            $headers = [];
            foreach ($raml['headers'] as $key => $headerRaml) {
                $headers[$key] = new Header($headerRaml, $key);
            }

            $this->setHeaders($headers);
        }

        // Check and set queryString node
        if (isset($raml['queryString'])) {
            $this->setQueryString($raml['queryString']);
        }

        // Check and set responses node
        if (isset($raml['responses'])) {
            $responses = [];
            foreach ($raml['responses'] as $code => $responseRaml) {
                $responseRaml = is_null($responseRaml) ? [] : $responseRaml;
                $responses[$code] = new Response($responseRaml, $rootNode, $code);
            }

            $this->setResponses($responses);
        }

        // Check and set request body node
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

    /**
     * Get value of Resource
     *
     * @return ResourceNode
     */
    public function getResource(): ResourceNode
    {
        return $this->resource;
    }

    /**
     * Set the value of Resource
     *
     * @param ResourceNode $resource
     *
     * @return self
     */
    public function setResource(ResourceNode $resource): self
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get all QueryParameters
     *
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * Get specific queryParameter by key
     *
     * @param string $key
     *
     * @return QueryParameter|null
     */
    public function getQueryParameter(string $key)
    {
        return isset($this->queryParameters[$key]) ? $this->queryParameters[$key] : null;
    }

    /**
     * Set all QueryParameters
     *
     * @param array $queryParameters
     *
     * @return self
     */
    public function setQueryParameters(array $queryParameters): self
    {
        $this->queryParameters = $queryParameters;
        return $this;
    }

    /**
     * Get all Headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get specific header by key
     *
     * @param string $key
     *
     * @return Header|null
     */
    public function getHeader(string $key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * Set all Headers
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
     * Get value of QueryString
     *
     * @return array
     */
    public function getQueryString(): array
    {
        return $this->queryString;
    }

    /**
     * Set the value of QueryString
     *
     * @param array $queryString
     *
     * @return self
     */
    public function setQueryString(array $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * Get all Responses
     *
     * @return array
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * Get specific response by HTTP code
     *
     * @param int $code
     *
     * @return Response|null
     */
    public function getResponse(int $code)
    {
        return isset($this->responses[$code]) ? $this->responses[$code] : null;
    }

    /**
     * Set all Responses
     *
     * @param array $responses
     *
     * @return self
     */
    public function setResponses(array $responses): self
    {
        $this->responses = $responses;
        return $this;
    }

    /**
     * Get value of Body
     *
     * @return Body|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of Body
     *
     * @param null $body
     *
     * @return self
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get value of Protocols
     *
     * @return array
     */
    public function getProtocols(): array
    {
        return $this->protocols;
    }

    /**
     * Check if the method supports a specific protocol
     *
     * @param string $protocol
     *
     * @return bool
     */
    public function hasProtocol(string $protocol): bool
    {
        return isset($this->protocols[strtoupper($protocol)]);
    }

    /**
     * Set the value of Protocols
     *
     * @param array $protocols
     *
     * @return self
     */
    public function setProtocols(array $protocols): self
    {
        $this->protocols = array_map('strtoupper', $protocols);
        return $this;
    }

    /**
     * Get value of Method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set the value of Method
     *
     * @param string $method
     *
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }
}
