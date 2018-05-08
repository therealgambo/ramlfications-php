<?php

namespace TheRealGambo\Ramlfications\Parameters;

class UriParameter extends BaseParameter
{
    /**
     * URI parameter with properties defined by the RAML specification's
     * "Named Parameters" section, e.g.: ``/foo/{id}`` where ``id`` is the
     * name of the URI parameter.
     *
     * @var boolean $required
     */
    private $required = true;

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
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }
}
