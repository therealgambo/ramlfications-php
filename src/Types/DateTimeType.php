<?php

namespace TheRealGambo\Ramlfications\Types;

use DateTime;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * DateTimeType type class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#datetime-type
 */
class DateTimeType extends TypeNode
{
    /**
     * DateTime format to use
     *
     * @var string
     **/
    private $format = '';

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (isset($raml['format'])) {
            $this->setFormat($raml['format']);
        }
    }

    /**
     * Get the value of Format
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set the value of Format
     *
     * @param string $format
     *
     * @return self
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        $format = $this->format ?: DATE_RFC3339;
        $d = DateTime::createFromFormat($format, $value);

        if ($d && $d->format($format) !== $value) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'datetime', $value);
        }
    }
}
