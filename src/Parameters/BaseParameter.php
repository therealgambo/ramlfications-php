<?php

namespace TheRealGambo\Ramlfications\Parameters;

/**
 * Class BaseParameter
 *
 * Base parameter with properties defined by the RAML spec's 'Named Parameters' section.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class BaseParameter implements InheritTypePropertiesInterface
{
    private $namedParameters = [
        "description", "type", "enum", "pattern", "minimum", "maximum", "example",
        "default", "required", "displayName", "maxLength",
        "minLength"
    ];

    private $name;
    private $raw         = [];
    private $description = '';
    private $displayName = '';
    private $minLength   = 0;
    private $maxLength   = 0;
    private $minimum     = 0;
    private $maximum     = 0;
    private $example     = null;
    private $examples    = null;
    private $default     = '';
    private $pattern     = null;
    private $enum        = [];
    private $type        = 'string';
    private $annotations = [];
    private $facets      = null;


    public function __construct(array $raml, $name)
    {
        $this->setRaw($raml)
             ->setName($name);

        if (isset($raml['description'])) {
            $this->setDescription($raml['description']);
        }

        if (isset($raml['displayName'])) {
            $this->setDisplayName($raml['displayName']);
        } else {
            $this->setDisplayName($name);
        }

        if (isset($raml['minLength'])) {
            $this->setMinLength($raml['minLength']);
        }

        if (isset($raml['maxLength'])) {
            $this->setMaxLength($raml['maxLength']);
        }

        if (isset($raml['minimum'])) {
            $this->setMinimum($raml['minimum']);
        }

        if (isset($raml['maximum'])) {
            $this->setMaximum($raml['maximum']);
        }

        if (isset($raml['default'])) {
            $this->setDefault($raml['default']);
        }

        if (isset($raml['enum'])) {
            $this->setEnum($raml['enum']);
        }

        if (isset($raml['example'])) {
            $this->setExample($raml['example']);
        }

        if (isset($raml['examples'])) {
            $this->setExamples($raml['examples']);
        }

        if (isset($raml['pattern'])) {
            $this->setPattern($raml['pattern']);
        }

        if (isset($raml['type'])) {
            $this->setType($raml['type']);
        }
    }


    public function inheritTypeProperties($inheritedProperty)
    {
//        for param in inherited_param:
//            name = getattr(param, "name", getattr(param, "code", None))
//            if name == self.name:
//                for n in NAMED_PARAMS:
//                    attr = getattr(self, n, None)
//                    if attr is None:
//                        attr = getattr(param, n, None)

        foreach ($inheritedProperty as $param) {
            $name = null;
            if (property_exists($param, 'name')) {
                $functionGetName = 'getName';
                $functionGetCode = 'getCode';
                $name = $param->$functionGetName();
                $code = $param->$functionGetCode();

                $name = !is_null($name) ? $name : !is_null($code) ? $code : null;
            }

            if ($name === $this->name) {
                foreach ($this->namedParameters as $namedParameter) {
                    if (is_null($this->$namedParameter)) {
                        $functionSet = 'set' . ucfirst($namedParameter);
                        $functionGet = 'get' . ucfirst($namedParameter);

                        $this->$functionSet($param->$functionGet());
                    }
                }
            }
        }
    }

    /**
     * Get value of NamedParams
     *
     * @return array
     */
    public function getNamedParams(): array
    {
        return $this->namedParameters;
    }

    /**
     * Set the value of NamedParams
     *
     * @param array $namedParameters
     *
     * @return self
     */
    public function setNamedParams(array $namedParameters)
    {
        $this->namedParameters = $namedParameters;
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
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * Get value of Description
     *
     * @return Content|null
     */
    public function getDescription()
    {
        return !is_null($this->description) ? new Content($this->description) : null;
    }

    /**
     * Set the value of Description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get value of MinLength
     *
     * @return integer
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Set the value of MinLength
     *
     * @param integer $minLength
     *
     * @return self
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Get value of MaxLength
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set the value of MaxLength
     *
     * @param integer $maxLength
     *
     * @return self
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * Get value of Minimum
     *
     * @return integer
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * Set the value of Minimum
     *
     * @param integer $minimum
     *
     * @return self
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
        return $this;
    }

    /**
     * Get value of Maximum
     *
     * @return integer
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * Set the value of Maximum
     *
     * @param integer $maximum
     *
     * @return self
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
        return $this;
    }

    /**
     * Get value of Example
     *
     * @return string
     */
    public function getExample(): string
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
    public function setExample($example)
    {
        $this->example = $example;
        return $this;
    }

    /**
     * Get value of Examples
     *
     * @return null
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * Set the value of Examples
     *
     * @param null $examples
     *
     * @return self
     */
    public function setExamples($examples)
    {
        $this->examples = $examples;
        return $this;
    }

    /**
     * Get value of Default
     *
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Set the value of Default
     *
     * @param string $default
     *
     * @return self
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Get value of Pattern
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Set the value of Pattern
     *
     * @param string $pattern
     *
     * @return self
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Get value of Enum
     *
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * Set the value of Enum
     *
     * @param array $enum
     *
     * @return self
     */
    public function setEnum(array $enum)
    {
        $this->enum = $enum;
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
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get value of Annotations
     *
     * @return null
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Set the value of Annotations
     *
     * @param null $annotations
     *
     * @return self
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
        return $this;
    }

    /**
     * Get value of Facets
     *
     * @return mixed
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Set the value of Facets
     *
     * @param mixed $facets
     *
     * @return self
     */
    public function setFacets($facets)
    {
        $this->facets = $facets;
        return $this;
    }

    /**
     * Get value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}