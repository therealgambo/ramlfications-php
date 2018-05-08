<?php

namespace TheRealGambo\Ramlfications\Types;

use DateTime;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;

/**
 * DateTimeOnlyType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#datetime-only-type
 */
class DatetimeOnlyType extends TypeNode
{
    const FORMAT = "Y-m-d\TH:i:s";

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);
    }

    public function validate($value)
    {
        parent::validate($value);

        $d = DateTime::createFromFormat(self::FORMAT, $value);

        if (!$d || $d->format(self::FORMAT) !== $value) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'datetime-only', $value);
        }
    }
}
