<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypePatternMismatchException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueExceedsMaximumException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueExceedsMinimumException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * StringType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#string-type
 */
class StringType extends TypeNode
{
    /**
     * Regular expression that this string should match.
     *
     * @var string
     **/
    private $pattern = '';

    /**
     * Minimum length of the string. Value MUST be equal to or greater than 0.
     * Default: 0
     *
     * @var int
     **/
    private $minLength = 0;

    /**
     * Maximum length of the string. Value MUST be equal to or greater than 0.
     * Default: 2147483647
     *
     * @var int
     **/
    private $maxLength = 2147483647;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (isset($raml['pattern'])) {
            $this->setPattern($raml['pattern']);
        }

        if (isset($raml['minLength'])) {
            $this->setMinLength($raml['minLength']);
        }

        if (isset($raml['maxLength'])) {
            $this->setMaxLength($raml['maxLength']);
        }
    }

    /**
     * Get the value of Pattern
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
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Get the value of Min Length
     *
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * Set the value of Min Length
     *
     * @param int $minLength
     *
     * @return self
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Get the value of Max Length
     *
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * Set the value of Max Length
     *
     * @param int $maxLength
     *
     * @return self
     */
    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        if (!is_string($value)) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'string', $value);
        }

        if (strlen($this->getPattern()) > 0) {
            if (preg_match('/' . $this->getPattern() . '/', $value) == false) {
                throw new TypePatternMismatchException($this->getPattern(), $value);
            }
        }

        if (!is_null($this->getMinLength())) {
            if (strlen($value) < $this->getMinLength()) {
                throw new TypeValueExceedsMinimumException($this->getMinLength(), $value);
            }
        }

        if (!is_null($this->getMaxLength())) {
            if (strlen($value) > $this->getMaxLength()) {
                throw new TypeValueExceedsMaximumException($this->getMaxLength(), $value);
            }
        }
    }
}
