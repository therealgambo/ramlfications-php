<?php

namespace TheRealGambo\Ramlfications\Parameters;

class QueryParameter extends BaseParameter
{
    /**
     * Query parameter with properties defined by the RAML specification's
     * "Named Parameters" section, e.g. ``/foo/bar?baz=123`` where ``baz``
     * is the name of the query parameter.
     *
     * @var boolean $required
     */
    private $required = false;

    public function __construct(array $raml, $name)
    {
        parent::__construct($raml, $name);
    }

    /**
     * Get value of Required
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the value of Required
     *
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
        return $this;
    }
}
