<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueExceedsMaximumException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueExceedsMinimumException;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeValueNotMultipleOfException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * NumberType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#number-type
 */
class NumberType extends TypeNode
{
    /**
     * The minimum value of the parameter. Applicable only to parameters of type number or integer.
     *
     * @var int
     **/
    private $minimum = 0;

    /**
     * The maximum value of the parameter.
     * Applicable only to parameters of type number or integer.
     *
     * @var int
     **/
    private $maximum = 0;

    /**
     * The format of the value. The value MUST be one of the following:
     *  - int32, int64, int, long, float, double, int16, int8
     *
     * @var string
     **/
    private $format = 'int';

    /**
     * A numeric instance is valid against "multipleOf" if the result of
     * dividing the instance by this keyword's value is an integer.
     *
     * @var int
     **/
    private $multipleOf = 0;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (isset($raml['minimum'])) {
            $this->setMinimum($raml['minimum']);
        }

        if (isset($raml['maximum'])) {
            $this->setMaximum($raml['maximum']);
        }

        if (isset($raml['format'])) {
            $this->setFormat($raml['format']);
        }

        if (isset($raml['multipleOf'])) {
            $this->setMultipleOf($raml['multipleOf']);
        }
    }

    /**
     * Get the value of Minimum
     *
     * @return int
     */
    public function getMinimum(): int
    {
        return $this->minimum;
    }

    /**
     * Set the value of Minimum
     *
     * @param int $minimum
     *
     * @return self
     */
    public function setMinimum(int $minimum): self
    {
        $this->minimum = $minimum;
        return $this;
    }

    /**
     * Get the value of Maximum
     *
     * @return int
     */
    public function getMaximum(): int
    {
        return $this->maximum;
    }

    /**
     * Set the value of Maximum
     *
     * @param int $maximum
     *
     * @return self
     */
    public function setMaximum(int $maximum): self
    {
        $this->maximum = $maximum;
        return $this;
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
     * @throws \Exception Thrown when given format is not any of allowed types.
     */
    public function setFormat(string $format): self
    {
        if (!in_array($format, ['int32', 'int64', 'int', 'long', 'float', 'double', 'int16', 'int8'])) {
            throw new \Exception(sprintf('Incorrect format given: "%s"', $format));
        }
        $this->format = $format;
        return $this;
    }

    /**
     * Get the value of Multiple Of
     *
     * @return int
     */
    public function getMultipleOf(): int
    {
        return $this->multipleOf;
    }

    /**
     * Set the value of Multiple Of
     *
     * @param int $multipleOf
     *
     * @return self
     */
    public function setMultipleOf(int $multipleOf): self
    {
        $this->multipleOf = $multipleOf;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        if (!is_null($this->maximum) && $value > $this->maximum) {
            throw new TypeValueExceedsMaximumException($this->maximum, $value);

        }

        if (!is_null($this->minimum) && $value < $this->minimum) {
            throw new TypeValueExceedsMinimumException($this->minimum, $value);
        }

        switch ($this->format) {
            case 'int8':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => -128, 'max_range' => 127
                    ]]) === false) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int8', $value);
                }
                break;
            case 'int16':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => -32768, 'max_range' => 32767
                    ]]) === false) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int16', $value);
                }
                break;
            case 'int32':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => -2147483648, 'max_range' => 2147483647
                    ]]) === false) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int32', $value);
                }
                break;
            case 'int64':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => -9223372036854775808, 'max_range' => 9223372036854775807
                    ]]) === false) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int64', $value);
                }
                break;
            case 'int':
                if (!is_int($value)) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int', $value);
                }
                break;
            case 'long':
                if (!is_int($value)) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'int or long', $value);
                }
                break;
            case 'float':
            case 'double':
                if (!is_float($value)) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'double or float', $value);
                }
                break;
            // if no format is given we check only if it is a number
            default:
                if (!is_float($value) && !is_int($value)) {
                    throw new TypeInvalidValueForTypeException($this->getName(), 'number', $value);
                }
                break;
        }

        if (!is_null($this->multipleOf) && ($value % $this->multipleOf) !== 0) {
            throw new TypeValueNotMultipleOfException($this->multipleOf, $value);
        }
    }
}
