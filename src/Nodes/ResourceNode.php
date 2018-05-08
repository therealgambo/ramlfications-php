<?php

namespace TheRealGambo\Ramlfications\Nodes;

use TheRealGambo\Ramlfications\Parameters\InheritTypePropertiesInterface;
use TheRealGambo\Ramlfications\Parameters\SecurityScheme;
use TheRealGambo\Ramlfications\Parameters\UriParameter;
use TheRealGambo\Ramlfications\Utilities\InheritanceUtility;

class ResourceNode extends BaseNode
{
    /**
     * Resource Node properties that are not already defined in BaseNode.
     *
     * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#resource-property
     */////////////////////////////////////////////////////////////////////////
//    private $displayName = '';
//    private $description = '';
//    private $annotations = [];

    /**
     * Array of methods that are applicable to this resource
     * @var array
     */
    private $methods = [];

//    private $is = [];

    /**
     * RAML resourceType that is applied to this resource
     * @var string
     */
    private $type = '';

//    private $securedBy = [];

    /**
     * Array of uriParameters that are applicable to this resource
     * @var array
     */
    private $uriParameters = [];

    /**
     * Array of children resources that this resource manages
     * @var array
     */
    private $childrenResources = [];

    /**
     * Additional internal properties
     */////////////////////////////////////////////////////////////////////////
    /**
     * Parent resource node reference
     * @var ResourceNode|null
     */
    private $parentResource = null;

    /**
     * The absolute URI that makes up this resource
     * @var
     */
    private $absoluteUri;

    /**
     * The shorthand relative path name for this resource
     * @var string
     */
    private $path;



    // ?
    private $traits;
    private $resourceType;
    private $securitySchemes = [];

//    public function __construct($name, array $raml, $method, RootNode $rootNode, ResourceNode $parent = null)
    public function __construct(array $raml, RootNode $rootNode, $path, ResourceNode $parent = null)
    {
        parent::__construct($raml, $rootNode);

        $this->setPath($path)
             ->setParentResource($parent);

        // Check and set methods node for this resource
        $methods = [];
        foreach ($raml as $method => $methodRaml) {
            if (in_array(strtolower($method), BaseNode::AVAILABLE_METHODS)) {
                $methods[strtolower($method)] = new ResourceMethodNode($methodRaml, $rootNode, $this, strtoupper($method));
            }
        }
        $this->setMethods($methods);

        // Check and set resourceType type node
        if (isset($raml['type'])) {
            $this->setResourceType($raml['type']);
        }

        // Check and set uriParameters node
        if (isset($raml['uriParameters'])) {
            $uriParameters = [];
            foreach ($raml['uriParameters'] as $key => $uriParameterRaml) {
                $uriParameters[$key] = new UriParameter($uriParameterRaml, $key);
            }

            $this->setUriParameters($uriParameters);
        }







        if (isset($raml['type'])) {
            $this->setType($raml['type']);
        }


        // Set absoluteUri
//        $this->setAbsoluteUri($this->determineAbsoluteUri());

//        $this->setProtocols($this->inheritAndDetermineProtocols());

//        $this->setMediaType($this->inheritAndDetermineMediaType());

//        $this->setSecuritySchemes($this->inheritAndDetermineSecuritySchemes());

//        $this->setType($this->inheritAndDetermineType());

//        $this->setResourceType($this->inheritAndDetermineResourceTypes());

//        $this->setIs($this->determineAssignedIs());

//        $this->setTraits($this->determineAssignedTrait());

//        $this->setType($this->determineAssignedType());

//        $this->setResourceType($this->determineAssignedResourceType());

        // @todo:
        // 1. request headers
        // 2. request body
        // 3. response body
        // 4. response headers
        // 5. description
        // 6. is
        // 7. traits
        // 8.

        if (is_null($this->getDisplayName())) {
//            $this->setDisplayName($this->getName());
        }

        if (!is_null($this->getResourceType())) {
            $this->inheritType();
        }
    }

//    private function determineAssignedResourceType()
//    {
//        if (!is_null($this->getType()) && count($this->getRootNode()->getResourceTypes()) > 0) {
//            return $this->getRootNode()->getResourceTypeByKey($this->getType());
//        }
//
//        return null;
//    }

//    private function determineAssignedType()
//    {
//        $method = $this->getRawElement($this->getMethod());
//        $assignedType = isset($method['type']) ? $method['type'] : null;
//        if (!is_null($assignedType)) {
//            return $assignedType;
//        }
//
//        $assignedType = $this->getRawElement('type');
//        if (!is_null($assignedType)) {
//            return $assignedType;
//        }
//
//        return null;
//    }
//
//    private function determineAssignedTrait(): array
//    {
//        $assigned = $this->getIs();
//        $traits = [];
//
//        if (count($assigned) > 0 && count($this->getRootNode()->getTraits()) > 0) {
//            foreach ($assigned as $trait) {
//                $rootTrait = $this->getRootNode()->getTraitByKey($trait);
//                if (!is_null($rootTrait)) {
//                    array_push($traits, $rootTrait);
//                }
//            }
//        }
//
//        return $traits;
//    }
//
//    private function determineAssignedIs(): array
//    {
//        $is = [];
//
//        $resource = $this->getRawElement('is');
//        if (!is_null($resource)) {
//            array_push($is, $resource);
//        }
//
//        $method = $this->getRawElement($this->getMethod());
//        if (!is_null($method)) {
//            if (isset($method['is'])) {
//                $is = array_merge($is, array_values($method['is']));
//            }
//        }
//
//        return $is;
//    }
//
//    private function inheritAndDetermineResourceTypes()
//    {
//        $type = $this->inheritAndDetermineType();
//        if (!is_null($type) && count($this->getRootNode()->getResourceTypes()) > 0) {
//            return $this->getRootNode()->getResourceTypeByKey($type);
//        }
//
//        return null;
//    }
//
//    private function inheritAndDetermineType()
//    {
//        // Inherit from method
//        if (!is_null($this->getMethod()) && isset($this->getRawElement($this->getMethod())['type'])) {
//            return $this->getRawElement($this->getMethod())['type'];
//        }
//
//        // set from resource
//        if (!is_null($this->getRawElement('type'))) {
//            return $this->getRawElement('type');
//        }
//
//        return null;
//    }
//
//    private function determineAbsoluteUri()
//    {
//        $return = $uri = $this->getRootNode()->getBaseUri() . $this->getPath();
//        $uriParts = explode('://', $uri);
//        if (count($uriParts) === 2) {
//            $uri = $uriParts[1];
//        }
//
//        $resourceProtocol = $this->inheritAndDetermineProtocols();
//        $protocols = (count($resourceProtocol) > 0 ) ? $resourceProtocol : $this->getRootNode()->getProtocols();
//
//        if (count($protocols) > 0) {
//            $return = [];
//            foreach ($protocols as $protocol) {
//                $return[] = strtolower($protocol) . '://' . $uri;
//            }
//        }
//
//        return $return;
//    }
//
//    /**
//     * Inherit and determine securityScheme that should be
//     * applied to this resource.
//     *
//     * @return array
//     */
//    private function inheritAndDetermineSecuritySchemes()
//    {
//        // Get all securityScheme that secure this resource
//        $secured = $this->inheritAndDetermineSecuritySchemesSecuredBy();
//        $securitySchemes = [];
//
//        // Iterate over each, checking that they key references a
//        // real securityScheme object
//        foreach ($secured as $securedBy) {
//            $securityScheme = $this->getRootNode()->getSecurityScheme($securedBy);
//
//            // Add securityScheme to array
//            if (!is_null($securityScheme)) {
//                $securitySchemes[$securedBy] = $securityScheme;
//            }
//        }
//
//        // Return array of securityScheme that secure this resource
//        return $securitySchemes;
//    }
//
//    /**
//     * Inherit and determine which `securedBy` key is most preferred
//     * for securing this resource.
//     *
//     * 1. Method definitions
//     * 2. Resource definitions
//     * 3. RootNode definitions
//     *
//     * @return array
//     */
//    private function inheritAndDetermineSecuritySchemesSecuredBy(): array
//    {
//        // Method level
//        if (!is_null($this->getMethod())) {
//            $method = $this->getRawElement($this->getMethod());
//            if (!is_null($method) && isset($method['securedBy'])) {
//                $securedBy = $method['securedBy'];
//
//                array_walk($securedBy, function (&$item) {
//                    if (is_null($item)) {
//                        $item = 'null';
//                    }
//                });
//
//                $this->setSecuredBy($securedBy);
//                return $this->getSecuredBy();
//            }
//        }
//
//        // Resource level
//        if (!is_null($this->getSecuredBy()) && count($this->getSecuredBy()) > 0) {
//            return $this->getSecuredBy();
//        }
//
//        // Root Level
//        if (!is_null($this->getRootNode()->getSecuredBy()) && count($this->getRootNode()->getSecuredBy()) > 0) {
//            $this->setSecuredBy($this->getRootNode()->getSecuredBy());
//            return $this->getSecuredBy();
//        }
//
//        return [];
//    }

//    /**
//     * Inherit and determine which mediaType definition is most
//     * preferred for being applied to this resource.
//     *
//     * @return array|string
//     */
//    private function inheritAndDetermineMediaType()
//    {
////        if (is_null($this->getMethod())) {
////            return [];
////        }
//
////        objects_to_inherit = ["method", "traits", "types", "resource", "root"]
//        $objectsToInherit = [
//            'method'   => $this->getRawElement($this->getMethod()),
//            'resource' => $this,
//            'root'     => $this->getRootNode()
//        ];
//
//        $inherited = InheritanceUtility::getInherited('mediaType', $objectsToInherit);
//
//        if (isset($inherited['method']) && !is_null($inherited['method'])) {
//            return $inherited['method'];
//        } elseif (isset($inherited['traits']) && !is_null($inherited['traits'])) {
//            return $inherited['traits'];
//        } elseif (isset($inherited['types']) && !is_null($inherited['types'])) {
//            return $inherited['types'];
//        } elseif (isset($inherited['resource']) && !is_null($inherited['resource'])) {
//            return $inherited['resource'];
//        } elseif (isset($inherited['root']) && !is_null($inherited['root'])) {
//            return $inherited['root'];
//        }
//
//        return [];
//    }
//
//    /**
//     * Inherit and determine which protocol is most
//     * preferred for being applied to this resource.
//     *
//     * @return array
//     */
//    private function inheritAndDetermineProtocols()
//    {
////        $objectsToInherit = ['traits', 'types', 'method', 'resource', 'parent'];
//        $objectsToInherit = [
//            'method'   => $this->getRawElement($this->getMethod()),
//            'resource' => $this,
//            'parent'   => $this->getParent()
//        ];
//
//        $inherited = InheritanceUtility::getInherited('protocols', $objectsToInherit);
//
//        if (isset($inherited['method']) && !is_null($inherited['method'])) {
//            return $inherited['method'];
//        } elseif (isset($inherited['types']) && !is_null($inherited['types'])) {
//            return $inherited['types'];
//        } elseif (isset($inherited['traits']) && !is_null($inherited['traits'])) {
//            return $inherited['traits'];
//        } elseif (isset($inherited['resource']) && !is_null($inherited['resource'])) {
//            return $inherited['resource'];
//        } elseif (isset($inherited['parent']) && !is_null($inherited['parent'])) {
//            return $inherited['parent'];
//        } else {
//            $parts = explode('://', $this->getRootNode()->getBaseUri());
//            return [strtoupper($parts[0])];
//        }
//    }

    public function inheritType()
    {
//        for p in METHOD_PROPERTIES:
//            inherited_prop = getattr(self.resource_type, p)
//            resource_prop = getattr(self, p)
//            if resource_prop and inherited_prop:
//                for r in resource_prop:
//                    r._inherit_type_properties(inherited_prop)

        foreach (BaseNode::METHOD_PROPERTIES as $property) {
            if (property_exists($this, $property)) {
                $resourceProperty = $this->$property;

                if (!is_null($resourceProperty) && isset($this->resourceType[$property])) {
                    $inheritedProperty = $this->resourceType[$property];

                    foreach ($resourceProperty as $r) {
                        /** @var InheritTypePropertiesInterface $r */
                        $r->inheritTypeProperties($inheritedProperty);
                    }
                }
            }
        }
    }

    public function getDisplayName(): string
    {
        return (strlen($this->displayName) === 0) ? $this->getPath() : $this->displayName;
    }

    /**
     * Get value of ParentResource
     *
     * @return ResourceNode|null
     */
    public function getParentResource()
    {
        return $this->parentResource;
    }

    /**
     * Set the value of ParentResource
     *
     * @param ResourceNode|null $parentResource
     *
     * @return self
     */
    public function setParentResource($parentResource): self
    {
        $this->parentResource = $parentResource;
        return $this;
    }

    /**
     * Get all Methods
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get a specific method that this resource provides
     *
     * @param string $method
     *
     * @return ResourceMethodNode|bool
     */
    public function getMethod(string $method)
    {
        return isset($this->methods[strtolower($method)]) ? $this->methods[strtolower($method)] : false;
    }

    /**
     * Check if this resource can handle a specific method
     *
     * @param string $method
     *
     * @return bool
     */
    public function hasMethod(string $method): bool
    {
        return isset($this->methods[$method]);
    }

    /**
     * Set all Methods
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
     * Get value of UriParameters
     *
     * @return array
     */
    public function getUriParameters(): array
    {
        return $this->uriParameters;
    }

    /**
     * Get a specific uriParameter
     *
     * @param string $key
     *
     * @return UriParameter|bool
     */
    public function getUriParameter(string $key)
    {
        return isset($this->uriParameters[$key]) ? $this->uriParameters[$key] : false;
    }

    /**
     * Check if resource has a specific uriParameter
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasUriParameter(string $key): bool
    {
        return isset($this->uriParameters[$key]);
    }

    /**
     * Set the value of UriParameters
     *
     * @param array $uriParameters
     *
     * @return self
     */
    public function setUriParameters(array $uriParameters): self
    {
        $this->uriParameters = $uriParameters;
        return $this;
    }

    /**
     * Get value of Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the value of Path
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get value of AbsoluteUri
     *
     * @return string
     */
    public function getAbsoluteUri(): string
    {
        return $this->absoluteUri;
    }

    /**
     * Set the value of AbsoluteUri
     *
     * @param string $absoluteUri
     *
     * @return self
     */
    public function setAbsoluteUri(string $absoluteUri): self
    {
        $this->absoluteUri = $absoluteUri;
        return $this;
    }

    /**
     * Get value of Traits
     *
     * @return mixed
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Set the value of Traits
     *
     * @param mixed $traits
     *
     * @return self
     */
    public function setTraits($traits)
    {
        $this->traits = $traits;
        return $this;
    }

    /**
     * Get value of Type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type
     *
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get value of ResourceTypes
     *
     * @return mixed
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Set the value of ResourceTypes
     *
     * @param mixed $resourceType
     *
     * @return self
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
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
     * Get a specific security scheme by key
     *
     * @param string $key
     *
     * @return SecurityScheme|null
     */
    public function getSecuritySchemeByKey($key)
    {
        return isset($this->securitySchemes[$key]) ? $this->securitySchemes[$key] : null;
    }

    /**
     * Set the value of SecuritySchemes
     *
     * @param array $securitySchemes
     *
     * @return self
     */
    public function setSecuritySchemes(array $securitySchemes)
    {
        $this->securitySchemes = $securitySchemes;
        return $this;
    }

    /**
     * Get value of ChildrenResources
     *
     * @return array
     */
    public function getChildrenResources(): array
    {
        return $this->childrenResources;
    }

    /**
     * Set the value of ChildrenResources
     *
     * @param array $childrenResources
     *
     * @return self
     */
    public function setChildrenResources(array $childrenResources)
    {
        $this->childrenResources = $childrenResources;
        return $this;
    }
}
