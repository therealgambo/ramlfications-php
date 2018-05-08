<?php

namespace TheRealGambo\Ramlfications\Nodes;

use TheRealGambo\Ramlfications\Exceptions\InvalidRootNodeException;
use TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException;
use TheRealGambo\Ramlfications\Parameters\Annotation;
use TheRealGambo\Ramlfications\Parameters\Content;
use TheRealGambo\Ramlfications\Parameters\Documentation;
use TheRealGambo\Ramlfications\Parameters\SecurityScheme;
use TheRealGambo\Ramlfications\Parameters\UriParameter;
use TheRealGambo\Ramlfications\Types\ArrayType;
use TheRealGambo\Ramlfications\Types\LazyProxyType;
use TheRealGambo\Ramlfications\Types\UnionType;
use TheRealGambo\Ramlfications\Utilities\StringTransformer;

class RootNode
{
    const HTTP  = 'HTTP';
    const HTTPS = 'HTTPS';

    private $raw;

    /**
     * The title for this API spec
     * @var string $title
     */
    private $title = '';

    /**
     * The description for this API spec
     * @var string $description
     */
    private $description = '';

    /**
     * The version of this API spec
     * @var string $version
     */
    private $version = '';

    /**
     * The baseUri for all resources defined in this API spec
     * @var string $baseUri
     */
    private $baseUri = '';

    /**
     * Any uriParameters that are exclusively used in the baseUri
     * @var array $baseUriParameters
     */
    private $baseUriParameters = [];

    /**
     * Available protocols that this API spec supports
     * @var array $protocols
     */
    private $protocols = [];

    /**
     * Available mime-types that this API spec supports
     * @var array $mediaType
     */
    private $mediaType = [];

    /**
     * Detailed documentation regarding this API spec
     * @var array $documentation
     */
    private $documentation = [];

    /**
     * Array of dataTypes that can be used
     * @var array $types
     */
    private $types = [];

    /**
     * Array of traits that can be used
     * @var array $traits
     */
    private $traits = [];

    /**
     * Array of resourceTypes that can be used
     * @var array $resourceTypes
     */
    private $resourceTypes = [];

    /**
     * Array of annotationTypes that can be used
     * @var array $annotationTypes
     */
    private $annotationTypes = [];

    /**
     * Array of annotations that are applied at the rootNode level
     * @var array $annotations
     */
    private $annotations = [];

    /**
     * Array of available securitySchemes for this API spec
     * @var array $securitySchemes
     */
    private $securitySchemes = [];

    /**
     * Array of securitySchemes that secure this API at the rootNode level
     * @var array $securedBy
     */
    private $securedBy = [];

    /**
     * @var array $uses
     */
    private $uses = [];

    /**
     * Array of resources that this API spec provides
     * @var array $resources
     */
    private $resources = [];


    private $typesWithInheritance = [];

    public function __construct($raml)
    {
        $this->setRaw($raml);

        // Required fields
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if (isset($raml['title'])) {
            $this->setTitle($raml['title']);
        }

        // Optional fields
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        if (isset($raml['version'])) {
            $this->setVersion($raml['version']);
        }

        if (isset($raml['baseUri'])) {
            if (strpos($raml['baseUri'], '{version}') !== false && strlen($this->getVersion()) > 0) {
                $raml['baseUri'] = str_replace('{version}', $this->getVersion(), $raml['baseUri']);
            }

            $this->setBaseUri($raml['baseUri']);
        }

        if (isset($raml['baseUriParameters'])) {
            $baseUriParameters = [];
            foreach ($raml['baseUriParameters'] as $key => $baseUriParameter) {
                $baseUriParameters[$key] = new UriParameter($baseUriParameter, $key);
            }

            $this->setBaseUriParameters($baseUriParameters);
        }

        if (isset($raml['protocols'])) {
            $this->setProtocols($raml['protocols']);
        } else {
            if (strpos(strtolower($this->getBaseUri()), 'http://') === 0) {
                $this->setProtocols([RootNode::HTTP]);
            } elseif (strpos(strtolower($this->getBaseUri()), 'https://') === 0) {
                $this->setProtocols([RootNode::HTTPS]);
            }
        }

        if (isset($raml['mediaType'])) {
            $this->setMediaType($raml['mediaType']);
        }

        if (isset($raml['documentation'])) {
            $documents = [];

            foreach ($raml['documentation'] as $document) {
                $documents[] = new Documentation($document);
            }

            $this->setDocumentation($documents);
        }

        //todo:
        if (isset($raml['types'])) {
            $types = [];
            foreach ($raml['types'] as $key => $typeRaml) {
                $types[$key] = $this->determineType($typeRaml, $key);
            }
            $this->setTypes($types);
        }

        if (isset($raml['traits'])) {
            $traits = [];
            foreach ($raml['traits'] as $key => $traitRaml) {
                // nasty hack for when the trait is provided as an array/list
//            if (is_integer($key) && count($traitRaml) === 1) {
//                $key = key($traitRaml);
//                $traitRaml = array_pop($traitRaml);
//            }

                $traits[$key] = new TraitNode($traitRaml, $this, $key);
            }
            $this->setTraits($raml['traits']);
        }

        if (isset($raml['resourceTypes'])) {
            $resourceTypes = [];
            foreach ($raml['resourceTypes'] as $key => $resourceTypeRaml) {
                $resourceTypes[$key] = new ResourceTypeNode($resourceTypeRaml, $this, $key);
            }
            $this->setResourceTypes($resourceTypes);
        }

        // todo:
        if (isset($raml['annotationTypes'])) {
            $annotations = [];
            foreach ($raml['annotationTypes'] as $key => $annotationTypeRaml) {
                $annotations[$key] = new Annotation($annotationTypeRaml, $this, $key);
            }
            $this->setAnnotationTypes($annotations);
        }

        if (isset($raml['securitySchemes'])) {
            $schemes = [];
            foreach ($raml['securitySchemes'] as $key => $securitySchemeRaml) {
                $schemes[$key] = new SecurityScheme($securitySchemeRaml, $this, $key);
            }
            $this->setSecuritySchemes($schemes);
        }

        if (isset($raml['securedBy'])) {
            $this->setSecuredBy($raml['securedBy']);
        }

        if (isset($raml['uses'])) {
            $this->setUses($raml['uses']);
        }


        // todo: annotations

        // todo: resources


        // applied annotations
        foreach ($raml as $key => $value) {
            if (preg_match('/\((.*)\)/', $key, $matches)) {
                if (!is_array($value)) {
                    $value = array($value);
                }

                if ($annotation = $this->getAnnotationType($key) !== false) {

                }
            }
        }

        $this->validate();
    }

    private function validate(): void
    {
        // An API title must always be set.
        if (strlen($this->getTitle()) === 0) {
            throw new InvalidRootNodeException('The required root node parameter \'title\' has not been set.');
        }

        if (strlen($this->getBaseUri()) > 0) {
            // If '{version}' exists in baseUri, then a version must be set.
            if (strpos($this->getBaseUri(), '{version}') !== false && strlen($this->getVersion()) === 0) {
                throw new InvalidRootNodeException(
                    'baseUri includes \'{version}\' parameter but no version has been set.'
                );
            }

            // Check for the existence of any extra uriParameters in the baseUri
            // that aren't declared in baseUriParameters
            preg_match_all('/{([\w]+)}/', $this->getBaseUri(), $elements, PREG_PATTERN_ORDER);
            $compare = array_diff($elements[1], array_keys($this->getBaseUriParameters()));

            // Missing uriParameters found that need to be declared.
            if (count($compare) > 0) {
                $params = '\'{' . implode('}, {', array_values($compare)) . '}\'';
                throw new InvalidRootNodeException(
                    'baseUri includes parameters ' . $params . ' but they have not been defined in baseUriParameters.'
                );
            }

            // Iterate over each baseUriParameter that exists in the baseUri node
            // and replace the string with its value
            foreach ($elements[1] as $element) {
                // @codeCoverageIgnoreStart
                if (($param = $this->getBaseUriParameter($element)) === false) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                $this->setBaseUri(
                    str_replace('{' . $element . '}', $param->getDefault(), $this->getBaseUri())
                );
            }
        }
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
     * Get value of Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
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
     * Get value of Version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set the value of Version
     *
     * @param string $version
     *
     * @return self
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get value of BaseUri
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * Set the value of BaseUri
     *
     * @param string $baseUri
     *
     * @return self
     */
    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;
        return $this;
    }

    /**
     * Get value of BaseUriParameters
     *
     * @return array
     */
    public function getBaseUriParameters(): array
    {
        return $this->baseUriParameters;
    }

    /**
     * Get the value of a BaseUriParameter using parameter key
     *
     * @param string $key
     *
     * @return UriParameter|bool
     */
    public function getBaseUriParameter(string $key)
    {
        return isset($this->baseUriParameters[$key]) ? $this->baseUriParameters[$key] : false;
    }

    /**
     * Set the value of BaseUriParameters
     *
     * @param array $baseUriParameters
     *
     * @return self
     */
    public function setBaseUriParameters(array $baseUriParameters): self
    {
        $this->baseUriParameters = $baseUriParameters;
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
     * Get value of MediaType
     *
     * @return array
     */
    public function getMediaType(): array
    {
        return $this->mediaType;
    }

    /**
     * Set the value of MediaType
     *
     * @param array|string $mediaType
     *
     * @return self
     */
    public function setMediaType($mediaType): self
    {
        $this->mediaType = is_string($mediaType) ? [$mediaType] : $mediaType;
        return $this;
    }

    /**
     * Get value of Documentation
     *
     * @return array
     */
    public function getDocumentation(): array
    {
        return $this->documentation;
    }

    /**
     * Set the value of Documentation
     *
     * @param array $documentation
     *
     * @return self
     */
    public function setDocumentation(array $documentation): self
    {
        $this->documentation = $documentation;
        return $this;
    }

    /**
     * Get value of Types
     *
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(string $key)
    {
        return isset($this->types[$key]) ? $this->types[$key] : false;
    }

    /**
     * Set the value of Types
     *
     * @param mixed $types
     *
     * @return self
     */
    public function setTypes(array $types): self
    {
        $this->types = $types;
        return $this;
    }

    /**
     * Get value of Traits
     *
     * @return array
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * Get a specific trait
     *
     * @param string $key
     *
     * @return TraitNode|bool
     */
    public function getTrait(string $key)
    {
        return isset($this->traits[$key]) ? $this->traits[$key] : false;
    }

    /**
     * Set the value of Traits
     *
     * @param array $traits
     *
     * @return self
     */
    public function setTraits(array $traits): self
    {
        $this->traits = $traits;
        return $this;
    }

    /**
     * Get value of ResourceTypes
     *
     * @return array
     */
    public function getResourceTypes(): array
    {
        return $this->resourceTypes;
    }

    /**
     * @param string $key
     *
     * @return ResourceTypeNode|bool
     */
    public function getResourceType($key)
    {
        return isset($this->resourceTypes[$key]) ? $this->resourceTypes[$key] : false;
    }

    /**
     * Set the value of ResourceTypes
     *
     * @param array $resourceTypes
     *
     * @return self
     */
    public function setResourceTypes(array $resourceTypes): self
    {
        $this->resourceTypes = $resourceTypes;
        return $this;
    }

    /**
     * Get value of AnnotationTypes
     *
     * @return array
     */
    public function getAnnotationTypes(): array
    {
        return $this->annotationTypes;
    }

    /**
     * Get a specific annotation by key
     *
     * @param string $key
     *
     * @return Annotation|bool
     */
    public function getAnnotationType(string $key)
    {
        return isset($this->annotationTypes[$key]) ? $this->annotationTypes[$key] : false;
    }

    /**
     * Set the value of AnnotationTypes
     *
     * @param array $annotationTypes
     *
     * @return self
     */
    public function setAnnotationTypes(array $annotationTypes): self
    {
        $this->annotationTypes = $annotationTypes;
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
     * Get value of SecuritySchemes
     *
     * @return array
     */
    public function getSecuritySchemes(): array
    {
        return $this->securitySchemes;
    }

    /**
     * Get a specific securityScheme
     *
     * @param string $scheme
     *
     * @return SecurityScheme|bool
     */
    public function getSecurityScheme(string $scheme)
    {
        return isset($this->securitySchemes[$scheme]) ? $this->securitySchemes[$scheme] : false;
    }

    /**
     * Set the value of SecuritySchemes
     *
     * @param array $securitySchemes
     *
     * @return self
     */
    public function setSecuritySchemes(array $securitySchemes): self
    {
        $this->securitySchemes = $securitySchemes;
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
     * Set the value of SecuredBy
     *
     * @param array $securedBy
     *
     * @throws InvalidSecuritySchemeException
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

        foreach ($securedBy as $securityScheme) {
            if (is_null($this->getSecurityScheme($securityScheme)) && $securityScheme !== 'null') {
                throw new InvalidSecuritySchemeException($securityScheme);
            }
        }

        $this->securedBy = $securedBy;
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
    public function setUses(array $uses): self
    {
        $this->uses = $uses;
        return $this;
    }

    /**
     * Get value of Resources
     *
     * @return array
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @param string $path
     *
     * @return ResourceNode|bool
     */
    public function getResourceByPath($path)
    {
        return isset($this->resources[$path]) ? $this->resources[$path] : false;
    }

    public function getResourceByPathAndMethod($path, $method)
    {
        return $this->getResourceByPath($path . ':' . strtolower($method));
    }

    /**
     * Set the value of Resources
     *
     * @param array $resources
     *
     * @return self
     */
    public function setResources(array $resources): self
    {
        $this->resources = $resources;
        return $this;
    }

    public function determineType($raml, string $key)
    {
        if (is_string($raml)) {
            $raml = ['type' => $raml];
        } elseif (is_array($raml)) {
            if (!array_key_exists('type', $raml)) {
                $raml['type'] = isset($raml['properties']) ? 'object' : 'string';
            }
        } else {
            throw new \Exception('Invalid datatype for $definition parameter.');
        }

        $type = isset($raml['type']) ? $raml['type'] : 'null';

        if (!in_array($type, ['','any'])) {
            if (in_array($type, TypeNode::VALID_TYPES)) {
                $className = sprintf(
                    'TheRealGambo\Ramlfications\Types\%sType',
                    StringTransformer::convertString($type, StringTransformer::UPPER_CAMEL_CASE)
                );

                return new $className($raml, $this, $key);
            }

            // if $type contains a '|' we can safely assume it's a combination of types (union)
            if (strpos($type, '|') !== false) {
                return new UnionType($raml, $this, $key);
            }

            // if $type contains a '[]' it means we have an array with a item restriction
            if (strpos($type, '[]') !== false) {
                return new ArrayType($raml, $this, $key);
            }

            // no standard type found so this must be a reference to a custom defined type
            // since the actual definition can be defined later then when it is referenced
            // we create a proxy object for lazy loading when it is needed
            return new LazyProxyType($raml, $this, $key);
        }

        // No subclass found, let's use base class
        return new TypeNode($raml, $this, $key);
    }

    public function applyInheritance()
    {
        foreach ($this->typesWithInheritance as $type) {
            /** @var TypeNode $type */
            $type->inheritFromParent();
        }
        // now clear list to prevent applying multiple times on the same objects
        $this->typesWithInheritance = [];
    }

    public function addTypeWithInheritance(TypeNode $type)
    {
        $this->typesWithInheritance[] = $type;
        return $this;
    }
}
