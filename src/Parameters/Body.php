<?php

namespace TheRealGambo\Ramlfications\Parameters;

use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;
use TheRealGambo\Ramlfications\Types\TypeInterface;

/**
 * Class Body
 *
 * Body of the request/response.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class Body implements InheritTypePropertiesInterface
{
    private $mimeType       = null;
    private $raw            = null;
    private $example        = null;
    private $type           = null;
    private $annotations = [];

    public function __construct($raml, RootNode $rootNode, string $mimeType)
    {
        $this->setRaw($raml)
             ->setMimeType($mimeType);

        if (isset($raml['example'])) {
            $this->setExample($raml['example']);
        }

        if (isset($raml['type'])) {
            $type = $rootNode->determineType($raml, $raml['type']);
            $type->inheritFromParent();
            $this->setType($type);
        } else {
            if (is_string($raml) && strlen($raml) > 0) {
                $type = $rootNode->determineType([], $raml);
                $type->inheritFromParent();
                $this->setType($type);
            }
            // nothing defined means a default of the any type
            // see https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md/#determine-default-types
            $this->setType(new TypeNode([], $rootNode, 'any'));
        }
    }

    public function inheritTypeProperties($inheritedProperty)
    {
//        body_params = ["schema", "example", "form_params"]
//        for param in inherited_param:
//            if param.mime_type != self.mime_type:
//                continue
//            for n in body_params:
//                attr = getattr(self, n, None)
//                if attr is None:
//                    attr = getattr(param, n, None)
//                    setattr(self, n, attr)

        $bodyParams = ['example'];
        foreach ($inheritedProperty as $param) {
            if (method_exists($param, 'getMimeType') && $param->getMimeType() !== $this->getMimeType()) {
                continue;
            }

            foreach ($bodyParams as $bodyParam) {
                $functionSet = 'set' . ucfirst($bodyParam);
                $functionGet = 'get' . ucfirst($bodyParam);
                if (property_exists($this, $bodyParam) &&
                    method_exists($param, $functionGet) &&
                    is_null($this->$bodyParam)) {
                    $this->$functionSet($param->$functionGet());
                }
            }
        }
    }

    /**
     * Get value of Type
     *
     * @return TypeNode
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type
     *
     * @param TypeNode $type
     *
     * @return self
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get value of MimeType
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set the value of MimeType
     *
     * @param string $mimeType
     *
     * @return self
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Get value of Raw
     *
     * @return array|string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Set the value of Raw
     *
     * @param array|string $raw
     *
     * @return self
     */
    public function setRaw($raw): self
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * Get value of Example
     *
     * @return null
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * Set the value of Example
     *
     * @param string $example
     *
     * @return self
     */
    public function setExample(string $example): self
    {
        $this->example = $example;
        return $this;
    }
}
