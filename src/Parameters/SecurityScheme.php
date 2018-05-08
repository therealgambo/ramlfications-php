<?php

namespace TheRealGambo\Ramlfications\Parameters;

use TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException;
use TheRealGambo\Ramlfications\Exceptions\MutuallyExclusiveException;
use TheRealGambo\Ramlfications\Nodes\RootNode;

/**
 * Class SecurityScheme
 *
 * Security scheme definition.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class SecurityScheme
{
    /**
     * Security Scheme Node properties.
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#security-scheme-declaration
     */////////////////////////////////////////////////////////////////////////
    /**
     * Type of authentication
     *
     * @var string $type
     */
    private $type;

    /**
     * Name of security scheme
     *
     * @var string $displayName
     */
    private $displayName = '';

    /**
     * Description of security scheme
     *
     * @var string $description
     */
    private $description = '';

    /**
     * Array of nodes (headers, queryParameters, queryString, responses) that define this securityScheme.
     * @var array $describedBy
     */
    private $describedBy = [
        'headers'         => [],
        'queryParameters' => [],
        'queryString'     => [],
        'responses'       => []
    ];

    /**
     * Security scheme-specific information
     *
     * @var array $settings
     */
    private $settings = [];

    private $annotations = [];


    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    /**
     * Supported securityScheme types. Type 'x-{other}' is handled in code.
     * @var array
     */
    private $validTypes = [
        'OAuth 1.0', 'OAuth 2.0', 'Basic Authentication', 'Digest Authentication',
        'Pass Through'
    ];

    /**
     * Reference to the RAML RootNode
     *
     * @var RootNode $rootNode
     */
    private $rootNode;

    /**
     * All defined data of item
     *
     * @var array $raw
     */
    private $raw;

    /**
     * Code used to reference this security scheme
     *
     * @var string $name
     */
    private $name;


    public function __construct(array $raml, RootNode $rootNode, $schemeName)
    {
        $this->setRaw($raml)
             ->setRootNode($rootNode)
             ->setName($schemeName);

        // Check and set valid securityScheme type
        if (isset($raml['type']) &&
            (in_array($raml['type'], $this->validTypes) || preg_match('/x-.*/', $raml['type']))) {
            $this->setType($raml['type']);
        } else {
            throw new InvalidSecuritySchemeException('Missing or invalid required securityScheme parameter: type');
        }

        // Check and set relevant nodes defined under describedBy
        if (isset($raml['describedBy'])) {
            // Parameters that MAY be defined in describedBy
            $describedBy = $raml['describedBy'];

            // queryParameters and queryString are mutually exclusive!
            if (isset($describedBy['queryParameters']) && isset($describedBy['queryString'])) {
                throw new MutuallyExclusiveException(
                    'Parameters \'queryParameters\' and \'queryString\' are mutually exclusive.'
                );
            }

            // Check and set headers node
            if (isset($describedBy['headers'])) {
                $headers = [];
                foreach ($describedBy['headers'] as $key => $headerRaml) {
                    $headers[$key] = new Header($headerRaml, $key);
                }

                $this->setHeaders($headers);
            }

            // Check and set queryParameters node
            if (isset($describedBy['queryParameters'])) {
                $queryParameters = [];
                foreach ($describedBy['queryParameters'] as $key => $queryParameterRaml) {
                    $queryParameters[$key] = new QueryParameter($queryParameterRaml, $key);
                }

                $this->setQueryParameters($queryParameters);
            }

            // Check and set queryString node
            if (isset($describedBy['queryString'])) {
                $this->setQueryString($describedBy['queryString']);
            }

            // Check and set responses node
            if (isset($describedBy['responses'])) {
                $responses = [];
                foreach ($describedBy['responses'] as $code => $responseRaml) {
                    $responses[$code] = new Response($responseRaml, $rootNode, $code);
                }

                $this->setResponses($responses);
            }

            // @todo: annotations
        }

        // Check and set description node
        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        // Check and set settings node
        if (isset($raml['settings'])) {
            $this->setSettings($raml['settings']);
        }

        // Check and set displayName node
        if (isset($raml['displayName'])) {
            $this->setDisplayName($raml['displayName']);
        }

        $this->validate();
    }

    public function validate()
    {
        if ($this->getType() === 'OAuth 1.0') {
            if ($this->getSetting('requestTokenUri') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 1.0\' is missing required setting \'requestTokenUri\'.'
                );
            }

            if ($this->getSetting('authorizationUri') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 1.0\' is missing required setting \'authorizationUri\'.'
                );
            }

            if ($this->getSetting('tokenCredentialsUri') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 1.0\' is missing required setting \'tokenCredentialsUri\'.'
                );
            }
        } elseif ($this->getType() === 'OAuth 2.0') {
            if ($this->getSetting('authorizationUri') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 2.0\' is missing required setting \'authorizationUri\'.'
                );
            }

            if ($this->getSetting('accessTokenUri') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 2.0\' is missing required setting \'accessTokenUri\'.'
                );
            }

            if ($this->getSetting('authorizationGrants') === false) {
                throw new InvalidSecuritySchemeException(
                    'Security scheme \'OAuth 2.0\' is missing required setting \'authorizationGrants\'.'
                );
            }
        }
    }

    /**
     * Get value of Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Name
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
     * Get value of displayName
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Set the value of displayName
     *
     * @param string $name
     *
     * @return self
     */
    public function setDisplayName(string $name): self
    {
        $this->displayName = $name;
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
     * Get value of Type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of Type
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
     * Get value of DescribedBy
     *
     * @return array
     */
    public function getDescribedBy(): array
    {
        return $this->describedBy;
    }

    /**
     * Get value of description
     *
     * @return Content
     */
    public function getDescription(): Content
    {
        return new Content($this->description);
    }

    /**
     * Set the value of description
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
     * Get value of Settings
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Get value of specific security scheme setting
     *
     * @param string $key
     *
     * @return mixed|bool
     */
    public function getSetting(string $key)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : false;
    }

    /**
     * Set the value of Settings
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Get value of Headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->describedBy['headers'];
    }

    /**
     * Get a specific header
     *
     * @param string $key
     *
     * @return Header|bool
     */
    public function getHeader(string $key)
    {
        return isset($this->describedBy['headers'][$key]) ? $this->describedBy['headers'][$key] : false;
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
        $this->describedBy['headers'] = $headers;
        return $this;
    }

    /**
     * Get value of QueryParameters
     *
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->describedBy['queryParameters'];
    }

    /**
     * Get a specific queryParameter
     *
     * @param string $key
     *
     * @return QueryParameter|bool
     */
    public function getQueryParameterByKey(string $key)
    {
        return isset($this->describedBy['queryParameters'][$key]) ? $this->describedBy['queryParameters'][$key] : false;
    }

    /**
     * Set the value of QueryParameters
     *
     * @param array $queryParameters
     *
     * @return self
     */
    public function setQueryParameters(array $queryParameters): self
    {
        $this->describedBy['queryParameters'] = $queryParameters;
        return $this;
    }

    /**
     * Get value of QueryString
     *
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->describedBy['queryString'];
    }

    /**
     * Set the value of QueryString
     *
     * @param mixed $queryString
     *
     * @return self
     */
    public function setQueryString($queryString): self
    {
        $this->describedBy['queryString'] = $queryString;
        return $this;
    }

    /**
     * Get value of Responses
     *
     * @return array
     */
    public function getResponses(): array
    {
        return $this->describedBy['responses'];
    }

    /**
     * Get a specific response by HTTP code
     *
     * @param int $code
     *
     * @return Response|bool
     */
    public function getResponse(int $code)
    {
        return isset($this->describedBy['responses'][$code]) ? $this->describedBy['responses'][$code] : false;
    }

    /**
     * Set the value of Responses
     *
     * @param array $responses
     *
     * @return self
     */
    public function setResponses(array $responses): self
    {
        $this->describedBy['responses'] = $responses;
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
